<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

trait AlwaysValidTrait
{
    final public static function create(
        EntityFactory $factory,
        DataTransferObjectInterface $dto
    ): self {
        $entity = new static();
        $factory->initialiseEntity($entity);
        $entity->update($dto);

        return $entity;
    }

    /**
     * Update and validate the Entity.
     *
     * Will roll back all updates if validation fails
     *
     * @param DataTransferObjectInterface $dto
     *
     * @return TemplateEntity
     * @throws ValidationException
     * @throws \ReflectionException
     */
    final public function update(DataTransferObjectInterface $dto): self
    {
        $dsm       = self::getDoctrineStaticMeta();
        $setters   = $dsm->getSetters();
        $backupDto = $this->getDto();
        foreach ($setters as $getterName => $setterName) {
            $this->$setterName($dto->$getterName());
        }
        try {
            $this->getValidator()->validate();
        } catch (ValidationException $e) {
            foreach ($setters as $getterName => $setterName) {
                $this->$setterName($backupDto->$getterName());
            }
            throw $e;
        }

        return $this;
    }

    final public function getDto(): DataTransferObjectInterface
    {
        $dsm     = self::getDoctrineStaticMeta();
        $setters = $dsm->getSetters();
        $dtoFqn  = $dsm->getDtoFqn();
        $dto     = new $dtoFqn();
        foreach ($setters as $getterName => $setterName) {
            $dto->$setterName($this->$getterName());
        }

        return $dto;
    }
}