<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\UuidPrimaryKeyInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use LogicException;
use ReflectionException;
use TypeError;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
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
    /**
     * @var array
     */
    private $createdDtos = [];

    public function __construct(
        NamespaceHelper $namespaceHelper,
        UuidFactory $uuidFactory
    ) {
        $this->namespaceHelper = $namespaceHelper;
        $this->uuidFactory     = $uuidFactory;
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
        $this->resetCreationTransaction();
        $this->createdDtos[$dtoFqn] = $dto;
        $this->setId($dto);
        $this->addRequiredItemsToDto($dto);
        $this->resetCreationTransaction();

        return $dto;
    }

    /**
     * When creating DTOs, we keep track of created DTOs. When you start creating a new DTO, you should call this first
     * and then call again after you have finished.
     *
     * This is handled for you in ::createEmptyDtoFromEntityFqn
     *
     * @return DtoFactory
     */
    public function resetCreationTransaction(): self
    {
        $this->createdDtos = [];

        return $this;
    }

    /**
     * If the Entity that the DTO represents has a settable and buildable UUID, then we should set that at the point of
     * creating a DTO for a new Entity instance
     *
     * @param DataTransferObjectInterface $dto
     */
    private function setId(DataTransferObjectInterface $dto): void
    {
        $entityFqn  = $dto::getEntityFqn();
        $reflection = $this->getDsmFromEntityFqn($entityFqn)
                           ->getReflectionClass();
        if ($reflection->implementsInterface(UuidPrimaryKeyInterface::class)) {
            $dto->setId($entityFqn::buildUuid($this->uuidFactory));
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

    private function addRequiredItemsToDto(DataTransferObjectInterface $dto): void
    {
        $this->addNestedRequiredDtos($dto);
        $this->addRequiredEmbeddableObjectsToDto($dto);
    }

    /**
     * Take the DTO for a defined EntityFqn and then parse the required relations and create nested DTOs for them
     *
     * Checks if the required relation is already set and if so, does nothing
     *
     * @param DataTransferObjectInterface $dto
     *
     * @throws ReflectionException
     * @throws DoctrineStaticMetaException
     */
    private function addNestedRequiredDtos(DataTransferObjectInterface $dto): void
    {
        $entityFqn         = $dto::getEntityFqn();
        $dsm               = $this->getDsmFromEntityFqn($entityFqn);
        $requiredRelations = $dsm->getRequiredRelationProperties();
        foreach ($requiredRelations as $propertyName => $requiredRelation) {
            $entityInterfaceFqn = $requiredRelation->getRelationEntityFqn();
            $getter = 'get' . $propertyName;
            if ($requiredRelation->isPluralRelation()) {
                if ($dto->$getter()->count() > 0) {
                    continue;
                }
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
     * Create and add a related DTO into the owning DTO collection property
     *
     * @param DataTransferObjectInterface $dto
     * @param string                      $propertyName
     * @param string                      $entityInterfaceFqn
     *
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
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
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function createDtoRelatedToDto(
        DataTransferObjectInterface $owningDto,
        string $relatedEntityFqn
    ): DataTransferObjectInterface {
        $this->resetCreationTransaction();

        return $this->createDtoRelatedToEntityDataObject($owningDto, $relatedEntityFqn);
    }

    /**
     * @param EntityData $owningDataObject
     * @param string     $relatedEntityFqn
     *
     * @return DataTransferObjectInterface
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function createDtoRelatedToEntityDataObject(
        EntityData $owningDataObject,
        string $relatedEntityFqn
    ): DataTransferObjectInterface {
        $relatedDtoFqn = $this->namespaceHelper->getEntityDtoFqnFromEntityFqn($relatedEntityFqn);
        $newlyCreated  = false;
        $dto           = $this->getCreatedDto($relatedDtoFqn);
        if (null === $dto) {
            $newlyCreated = true;
            $dto          = $this->createDtoInstance($relatedDtoFqn);
        }
        /**
         * @var DoctrineStaticMeta $owningDsm
         */
        $owningEntityFqn = $owningDataObject::getEntityFqn();
        $owningDsm       = $owningEntityFqn::getDoctrineStaticMeta();
        $owningSingular  = $owningDsm->getSingular();
        $owningPlural    = $owningDsm->getPlural();

        /**
         * @var DoctrineStaticMeta $relatedDsm
         */
        $relatedDsm = $relatedEntityFqn::getDoctrineStaticMeta();

        $dtoSuffix = $owningDataObject instanceof DataTransferObjectInterface ? 'Dto' : '';

        $relatedRequiredRelations = $relatedDsm->getRequiredRelationProperties();
        foreach (array_keys($relatedRequiredRelations) as $propertyName) {
            switch ($propertyName) {
                case $owningSingular:
                    $getter = 'get' . $owningSingular . $dtoSuffix;
                    try {
                        if (null !== $dto->$getter()) {
                            break 2;
                        }
                    } catch (TypeError $e) {
                        //null will cause a type error on getter
                    }
                    $setter = 'set' . $owningSingular . $dtoSuffix;
                    $dto->$setter($owningDataObject);

                    break 2;
                case $owningPlural:
                    $collectionGetter = 'get' . $owningPlural;
                    $collection       = $dto->$collectionGetter();
                    foreach ($collection as $item) {
                        if ($item === $owningDataObject) {
                            break 3;
                        }
                    }
                    $collection->add($owningDataObject);

                    break 2;
            }
        }
        if (true === $newlyCreated) {
            $this->addRequiredItemsToDto($dto);
        }

        return $dto;
    }

    private function getCreatedDto(string $dtoFqn): ?DataTransferObjectInterface
    {
        return $this->createdDtos[$dtoFqn] ?? null;
    }

    private function createDtoInstance(string $dtoFqn): DataTransferObjectInterface
    {
        if (null !== $this->getCreatedDto($dtoFqn)) {
            throw new LogicException('Trying to set a created DTO ' . $dtoFqn . ' when one already exists');
        }
        $dto = new $dtoFqn();
        $this->setId($dto);
        $this->createdDtos[ltrim($dtoFqn, '\\')] = $dto;

        return $dto;
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

    private function addRequiredEmbeddableObjectsToDto(DataTransferObjectInterface $dto): void
    {
        $dsm                  = $this->getDsmFromEntityFqn($dto::getEntityFqn());
        $embeddableProperties = $dsm->getEmbeddableProperties();
        foreach ($embeddableProperties as $property => $embeddableObject) {
            $setter = 'set' . $property;
            $dto->$setter($embeddableObject::create($embeddableObject::DEFAULTS));
        }
    }

    /**
     * Create a DTO with a preset relation to the owning Entity and all other items filled with new objects
     *
     * @param EntityInterface $owningEntity
     * @param string          $relatedEntityFqn
     *
     * @return DataTransferObjectInterface
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function createDtoRelatedToEntityInstance(
        EntityInterface $owningEntity,
        string $relatedEntityFqn
    ): DataTransferObjectInterface {
        $this->resetCreationTransaction();

        return $this->createDtoRelatedToEntityDataObject($owningEntity, $relatedEntityFqn);
    }

    /**
     * Create a DTO with the values from teh Entity, optionally with some values directly overridden with your values
     * to set
     *
     * @param EntityInterface $entity
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function createDtoFromEntity(EntityInterface $entity)
    {
        $this->resetCreationTransaction();
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
