<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\UuidPrimaryKeyInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

class DtoFactory implements DtoFactoryInterface
{
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;
    /**
     * @var UuidFactory
     */
    private $uuidFactory;

    public function __construct(NamespaceHelper $namespaceHelper, UuidFactory $uuidFactory)
    {
        $this->namespaceHelper = $namespaceHelper;
        $this->uuidFactory     = $uuidFactory;
    }

    /**
     * Take the DTO for a defined EntityFqn and then parse the required relations and create nested DTOs for them
     *
     * Checks if the required relation is already set and if so, does nothing
     *
     * @param DataTransferObjectInterface $dto
     *
     * @throws \ReflectionException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function addNestedRequiredDtos(DataTransferObjectInterface $dto): void
    {
        $entityFqn         = $dto::getEntityFqn();
        $dsm               = $this->getDsmFromEntityFqn($entityFqn);
        $requiredRelations = $dsm->getRequiredRelationProperties();
        foreach ($requiredRelations as $propertyName => $types) {
            $numTypes = count($types);
            if (1 !== $numTypes) {
                throw new \RuntimeException('Unexpected number of types, only expecting 1: ' . print_r($types, true));
            }
            $entityInterfaceFqn = $types[0];
            $getter             = 'get' . $propertyName;
            if ('[]' === substr($entityInterfaceFqn, -2)) {
                if ($dto->$getter()->count() > 0) {
                    continue;
                }
                $entityInterfaceFqn = substr($entityInterfaceFqn, 0, -2);
                $this->addNestedDtoToCollection($dto, $propertyName, $entityInterfaceFqn);
                continue;
            }
            $issetAsDtoMethod    = 'isset' . $propertyName . 'AsDto';
            $issetAsEntityMethod = 'isset' . $propertyName . 'AsEntity';
            if (true === $dto->$issetAsDtoMethod() || true === $dto->$issetAsEntityMethod()) {
                continue;
            }
            $this->addNestedDto($dto, $propertyName, $entityInterfaceFqn);
        }
    }

    /**
     * Get the instance of DoctrineStaticMeta from the Entity by FQN
     *
     * @param string $entityFqn
     *
     * @return DoctrineStaticMeta
     */
    private function getDsmFromEntityFqn(string $entityFqn): DoctrineStaticMeta
    {
        return $entityFqn::getDoctrineStaticMeta();
    }

    /**
     * Create and add a related DTO into the owning DTO collection property
     *
     * @param DataTransferObjectInterface $dto
     * @param string                      $propertyName
     * @param string                      $entityInterfaceFqn
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    private function addNestedDtoToCollection(
        DataTransferObjectInterface $dto,
        string $propertyName,
        string $entityInterfaceFqn
    ): void {
        $collectionGetter = 'get' . $propertyName;
        $dto->$collectionGetter()->add(
            $this->createDtoRelatedToDto(
                $dto,
                $this->namespaceHelper->getEntityFqnFromEntityInterfaceFqn($entityInterfaceFqn)
            )
        );
    }

    /**
     * Create a DTO with a preset relation to the owning Entity DTO and all other items filled with new objects
     *
     * @param DataTransferObjectInterface $owningDto
     * @param string                      $relatedEntityFqn
     *
     * @return DataTransferObjectInterface
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function createDtoRelatedToDto(
        DataTransferObjectInterface $owningDto,
        string $relatedEntityFqn
    ): DataTransferObjectInterface {
        $relatedDtoFqn = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn($relatedEntityFqn);
        $dto           = new $relatedDtoFqn();
        $this->setIdIfSettable($dto, $relatedEntityFqn);
        /**
         * @var DoctrineStaticMeta $owningDsm
         */
        $owningEntityFqn = $this->namespaceHelper->getEntityFqnFromEntityDtoFqn(\get_class($owningDto));
        $owningDsm       = $owningEntityFqn::getDoctrineStaticMeta();
        $owningSingular  = $owningDsm->getSingular();
        $owningPlural    = $owningDsm->getPlural();

        /**
         * @var DoctrineStaticMeta $relatedDsm
         */
        $relatedDsm = $relatedEntityFqn::getDoctrineStaticMeta();
        $relatedDsm->getRequiredRelationProperties();

        $relatedRequiredRelations = $relatedDsm->getRequiredRelationProperties();
        foreach (array_keys($relatedRequiredRelations) as $propertyName) {
            switch ($propertyName) {
                case $owningSingular:
                    $setter = 'set' . $owningSingular . 'Dto';
                    $dto->$setter($owningDto);
                    $this->addRequiredItemsToDto($dto, $relatedEntityFqn);

                    return $dto;
                case $owningPlural:
                    $collectionGetter = 'get' . $owningPlural;
                    $dto->$collectionGetter()->add($owningDto);
                    $this->addRequiredItemsToDto($dto, $relatedEntityFqn);

                    return $dto;
            }
        }
        $this->addRequiredItemsToDto($dto);

        return $dto;
    }

    /**
     * If the Entity that the DTO represents has a settable and buildable UUID, then we should set that at the point of
     * creating a DTO for a new Entity instance
     *
     * @param DataTransferObjectInterface $dto
     */
    private function setIdIfSettable(DataTransferObjectInterface $dto): void
    {
        $entityFqn  = $dto::getEntityFqn();
        $reflection = $this->getDsmFromEntityFqn($entityFqn)
                           ->getReflectionClass();
        if ($reflection->implementsInterface(UuidPrimaryKeyInterface::class)) {
            $dto->setId($entityFqn::buildUuid($this->uuidFactory));
        }
    }

    public function addRequiredItemsToDto(DataTransferObjectInterface $dto)
    {
        $this->addNestedRequiredDtos($dto);
        $this->addRequiredEmbeddableObjectsToDto($dto);
    }

    public function addRequiredEmbeddableObjectsToDto(DataTransferObjectInterface $dto): void
    {
        $dsm                  = $this->getDsmFromEntityFqn($dto::getEntityFqn());
        $embeddableProperties = $dsm->getEmbeddableProperties();
        foreach ($embeddableProperties as $property => $embeddableObject) {
            $setter = 'set' . $property;
            $dto->$setter(new $embeddableObject());
        }
    }

    private function addNestedDto(
        DataTransferObjectInterface $dto,
        string $propertyName,
        string $entityInterfaceFqn
    ): void {
        $dtoSetter = 'set' . $propertyName . 'Dto';
        $dto->$dtoSetter(
            $this->createDtoRelatedToDto(
                $dto,
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
     */
    public function createEmptyDtoFromEntityFqn(string $entityFqn)
    {
        $dtoFqn = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn($entityFqn);

        $dto = new $dtoFqn();
        $this->setIdIfSettable($dto);
        $this->addRequiredItemsToDto($dto);

        return $dto;
    }

    /**
     * Create a DTO with a preset relation to the owning Entity and all other items filled with new objects
     *
     * @param EntityInterface $owningEntity
     * @param string          $relatedEntityFqn
     *
     * @return DataTransferObjectInterface
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function createDtoRelatedToEntityInstance(
        EntityInterface $owningEntity,
        string $relatedEntityFqn
    ): DataTransferObjectInterface {
        $owningDsm      = $this->getDsmFromEntityInstance($owningEntity);
        $owningSingular = $owningDsm->getSingular();
        $owningPlural   = $owningDsm->getPlural();

        $relatedDsm = $this->getDsmFromEntityFqn($relatedEntityFqn);
        $relatedDsm->getRequiredRelationProperties();
        $relatedDtoFqn = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn($relatedEntityFqn);
        $dto           = new $relatedDtoFqn();
        $this->setIdIfSettable($dto);

        $relatedRequiredRelations = $relatedDsm->getRequiredRelationProperties();
        foreach (array_keys($relatedRequiredRelations) as $propertyName) {
            switch ($propertyName) {
                case $owningSingular:
                    $setter = 'set' . $owningSingular;
                    $dto->$setter($owningEntity);
                    $this->addRequiredItemsToDto($dto);

                    return $dto;
                case $owningPlural:
                    $getter = 'get' . $owningPlural;
                    $dto->$getter()->add($owningEntity);
                    $this->addRequiredItemsToDto($dto);

                    return $dto;
            }
        }

    }

    /**
     * Get the instance of DoctrineStaticMeta from the Entity by FQN
     *
     * @param string $entity
     *
     * @return DoctrineStaticMeta
     */
    private function getDsmFromEntityInstance(EntityInterface $entity): DoctrineStaticMeta
    {
        return $entity::getDoctrineStaticMeta();
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
