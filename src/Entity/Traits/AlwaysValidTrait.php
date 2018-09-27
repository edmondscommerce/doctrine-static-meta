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
     * Will roll back all updates if validation fails
     *
     * @param DataTransferObjectInterface $dto
     *
     * @throws ValidationException
     */
    final public function update(DataTransferObjectInterface $dto): void
    {
        $dsm       = self::getDoctrineStaticMeta();
        $setters   = $dsm->getSetters();
        $backupDto = $this->getDto();
        foreach ($setters as $getterName => $setterName) {
            if (method_exists($dto, $getterName)) {
                $this->$setterName($dto->$getterName());
            }
        }
        try {
            $this->getValidator()->validate();
        } catch (ValidationException $e) {
            foreach ($setters as $getterName => $setterName) {
                $this->$setterName($backupDto->$getterName());
            }
            throw $e;
        }
    }
}