<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

class DtoFactory implements DtoFactoryInterface
{
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;

    public function __construct(NamespaceHelper $namespaceHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
    }

    public function createEmptyDtoFromEntityFqn(string $entityFqn)
    {
        $dtoFqn = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn($entityFqn);

        return new $dtoFqn();
    }

    /**
     * Create a DTO with the values from teh Entity, optionally with some values directly overridden with your values
     * to set
     *
     * @param EntityInterface $entity
     *
     * @return mixed
     */
    public function createDtoFromEntity(EntityInterface $entity)
    {
        $dsm     = $entity::getDoctrineStaticMeta();
        $dtoFqn  = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn($dsm->getReflectionClass()->getName());
        $dto     = new $dtoFqn();
        $setters = $dsm->getSetters();
        foreach ($setters as $getterName => $setterName) {
            $dto->$setterName($entity->$getterName());
        }

        return $dto;
    }
}