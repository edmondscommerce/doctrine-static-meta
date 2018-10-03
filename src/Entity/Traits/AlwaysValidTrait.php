<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

trait AlwaysValidTrait
{
    final public static function create(
        EntityFactory $factory,
        DataTransferObjectInterface $dto = null
    ): self {
        $entity = new static();
        $factory->initialiseEntity($entity);
        if (null !== $dto) {
            $entity->update($dto);
        }

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
}