<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityDataValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

trait AlwaysValidTrait
{
    /**
     * @var EntityDataValidatorInterface
     */
    private $entityDataValidator;

    final public static function create(
        EntityFactoryInterface $factory,
        DataTransferObjectInterface $dto = null
    ): self {
        $entity = new static();
        $factory->initialiseEntity($entity);
        if (null !== $dto) {
            $entity->update($dto);

            return $entity;
        }
        $entity->getValidator()->validate();

        return $entity;
    }

    /**
     * Update and validate the Entity.
     *
     * The DTO can
     *  - contain data not related to this Entity, it will be ignored
     *  - not have to have all the data for this Entity, it will only update where the DTO has the setter
     *
     * The entity state after update will be validated
     *
     * Will roll back all updates if validation fails
     *
     * @param DataTransferObjectInterface $dto
     *
     * @throws ValidationException
     */
    final public function update(DataTransferObjectInterface $dto): void
    {
        $backup  = [];
        $setters = self::getDoctrineStaticMeta()->getSetters();
        foreach ($setters as $getterName => $setterName) {
            if (method_exists($dto, $getterName)) {
                $backup[$setterName] = $this->$getterName();
                $this->$setterName($dto->$getterName());
            }
        }
        try {
            $this->getValidator()->validate();
        } catch (ValidationException $e) {
            foreach ($backup as $setterName => $backupValue) {
                $this->$setterName($backupValue);
            }
            throw $e;
        }
    }

    private function getValidator(): EntityDataValidatorInterface
    {
        if (!$this->entityDataValidator instanceof EntityDataValidatorInterface) {
            throw new \RuntimeException(
                'You must call injectDataValidator before being able to update an Entity'
            );
        }

        return $this->entityDataValidator;
    }

    /**
     * This method is called automatically by the EntityFactory when initialisig the Entity, by way of the
     * EntityDependencyInjector
     *
     * @param EntityDataValidatorInterface $entityDataValidator
     */
    public function injectEntityDataValidator(EntityDataValidatorInterface $entityDataValidator)
    {
        $this->entityDataValidator = $entityDataValidator;
        $this->entityDataValidator->setEntity($this);
    }
}
