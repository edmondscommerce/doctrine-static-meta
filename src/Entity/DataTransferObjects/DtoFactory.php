<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
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

    /**
     * Take the DTO for a defined EntityFqn and then parse the required relations and create nested DTOs for them
     *
     * @param string                      $entityFqn
     * @param DataTransferObjectInterface $dto
     *
     * @throws \ReflectionException
     */
    public function addNestedRequiredDtos(string $entityFqn, DataTransferObjectInterface $dto): void
    {
        /**
         * @var DoctrineStaticMeta $dsm
         */
        $dsm               = $entityFqn::getDoctrineStaticMeta();
        $requiredRelations = $dsm->getRequiredRelationProperties();
        foreach ($requiredRelations as $propertyName => $types) {
            $numTypes = count($types);
            if (1 !== $numTypes) {
                throw new \RuntimeException('Unexpected number of types, only expecting 1: ' . print_r($types, true));
            }
            $entityInterfaceFqn = $types[0];
            if ('[]' === substr($entityInterfaceFqn, -2)) {
                $entityInterfaceFqn = substr($entityInterfaceFqn, 0, -2);
                $this->addNestedDtoToCollection($dto, $propertyName, $entityInterfaceFqn);
                continue;
            }
            $this->addNestedDto($dto, $propertyName, $entityInterfaceFqn);
        }
    }

    private function addNestedDtoToCollection(
        DataTransferObjectInterface $dto,
        string $propertyName,
        string $entityInterfaceFqn
    ): void {
        $collectionGetter = 'get' . $propertyName;
        $dto->$collectionGetter()->add(
            $this->createEmptyDtoFromEntityFqn(
                $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($entityInterfaceFqn)
            )
        );
    }

    /**
     * Pass in the FQN for an entity and get an empty DTO, including nested empty DTOs for required relations
     *
     * @param string $entityFqn
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function createEmptyDtoFromEntityFqn(string $entityFqn)
    {
        $dtoFqn = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn($entityFqn);

        $dto = new $dtoFqn();
        $this->addNestedRequiredDtos($entityFqn, $dto);

        return $dto;
    }

    private function addNestedDto(
        DataTransferObjectInterface $dto,
        string $propertyName,
        string $entityInterfaceFqn
    ): void {
        $dtoSetter = 'set' . $propertyName . 'Dto';
        $dto->$dtoSetter(
            $this->createEmptyDtoFromEntityFqn(
                $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($entityInterfaceFqn)
            )
        );
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
