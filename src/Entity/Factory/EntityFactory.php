<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\AlwaysValidInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\GenericFactoryInterface;
use ts\Reflection\ReflectionClass;

class EntityFactory implements GenericFactoryInterface, EntityFactoryInterface
{
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var EntityDependencyInjector
     */
    protected $entityDependencyInjector;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var DtoFactory
     */
    private $dtoFactory;

    /**
     * This array is used to track Entities that in the process of being created as part of a transaction
     *
     * @var array
     */
    private $created = [];

    public function __construct(
        NamespaceHelper $namespaceHelper,
        EntityDependencyInjector $entityDependencyInjector,
        DtoFactory $dtoFactory
    ) {
        $this->namespaceHelper          = $namespaceHelper;
        $this->entityDependencyInjector = $entityDependencyInjector;
        $this->dtoFactory               = $dtoFactory;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): EntityFactoryInterface
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Get an instance of the specific Entity Factory for a specified Entity
     *
     * Not type hinting the return because the whole point of this is to have an entity specific method, which we
     * can't hint for
     *
     * @param string $entityFqn
     *
     * @return mixed
     */
    public function createFactoryForEntity(string $entityFqn)
    {
        $this->assertEntityManagerSet();
        $factoryFqn = $this->namespaceHelper->getFactoryFqnFromEntityFqn($entityFqn);

        return new $factoryFqn($this, $this->entityManager);
    }

    private function assertEntityManagerSet(): void
    {
        if (!$this->entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException(
                'No EntityManager set, this must be set first using setEntityManager()'
            );
        }
    }

    public function getEntity(string $className)
    {
        return $this->create($className);
    }

    /**
     * Build a new entity, optionally pass in a DTO to provide the data that should be used
     *
     * Optionally pass in an array of property=>value
     *
     * @param string                           $entityFqn
     *
     * @param DataTransferObjectInterface|null $dto
     *
     * @return mixed
     */
    public function create(string $entityFqn, DataTransferObjectInterface $dto = null)
    {
        $this->assertEntityManagerSet();

        return $this->createEntity($entityFqn, $dto, true);
    }

    /**
     * Create the Entity
     *
     * @param string                           $entityFqn
     *
     * @param DataTransferObjectInterface|null $dto
     *
     * @param bool                             $isRootEntity
     *
     * @return EntityInterface
     */
    private function createEntity(
        string $entityFqn,
        DataTransferObjectInterface $dto = null,
        $isRootEntity = true
    ): EntityInterface {
        $entity = $this->getNewInstance($entityFqn);
        if (null === $dto) {
            $dto = $this->dtoFactory->createEmptyDtoFromEntityFqn($entityFqn);
        }
        $entity->setId($dto->getId());
        $idString = $entity->getId()->__toString();
        if (isset($this->created[$idString])) {
            return $this->created[$idString];
        }
        $this->created[$idString] = $entity;
        $this->initialiseEntity($entity);
        $this->updateDto($entity, $dto);
        if ($isRootEntity) {
            $this->stopTransaction();
        }
        $entity->update($dto);

        return $entity;
    }

    private function getNewInstance(string $entityFqn): EntityInterface
    {
        $reflection = $this->getDoctrineStaticMetaForEntityFqn($entityFqn)
                           ->getReflectionClass();
        $entity     = $reflection->newInstanceWithoutConstructor();
        $runInit    = $reflection->getMethod('runInitMethods');
        $runInit->setAccessible(true);
        $runInit->invoke($entity);
        $transactionProperty = $reflection->getProperty(AlwaysValidInterface::TRANSACTION_RUNNING_PROPERTY);
        $transactionProperty->setAccessible(true);
        $transactionProperty->setValue($entity, true);
        if ($entity instanceof EntityInterface) {
            return $entity;
        }
        throw new \LogicException('Failed to create an instance of EntityInterface');
    }

    private function getDoctrineStaticMetaForEntityFqn(string $entityFqn): DoctrineStaticMeta
    {
        return $entityFqn::getDoctrineStaticMeta();
    }

    /**
     * Take an already instantiated Entity and perform the final initialisation steps
     *
     * @param EntityInterface $entity
     */
    public function initialiseEntity(EntityInterface $entity): void
    {
        $entity->ensureMetaDataIsSet($this->entityManager);
        $this->addListenerToEntityIfRequired($entity);
        $this->entityDependencyInjector->injectEntityDependencies($entity);
    }

    /**
     * Generally DSM Entities are using the Notify change tracking policy.
     * This ensures that they are fully set up for that
     *
     * @param EntityInterface $entity
     */
    private function addListenerToEntityIfRequired(EntityInterface $entity): void
    {
        if (!$entity instanceof NotifyPropertyChanged) {
            return;
        }
        $listener = $this->entityManager->getUnitOfWork();
        $entity->addPropertyChangedListener($listener);
    }

    private function updateDto(
        EntityInterface $entity,
        DataTransferObjectInterface $dto
    ): void {
        $this->replaceNestedDtoWithEntityInstanceIfIdsMatch($dto, $entity);
        $this->replaceNestedDtosWithNewEntities($dto);
    }

    private function replaceNestedDtoWithEntityInstanceIfIdsMatch(
        DataTransferObjectInterface $dto,
        EntityInterface $entity
    ): void {
        $getters = $this->getGettersForDtosOrCollections($dto);
        if ([[], []] === $getters) {
            return;
        }
        list($dtoGetters, $collectionGetters) = array_values($getters);
        $entityFqn = \get_class($entity);
        foreach ($dtoGetters as $getter) {
            $propertyName        = substr($getter, 3, -3);
            $issetAsEntityMethod = 'isset' . $propertyName . 'AsEntity';
            if (true === $dto->$issetAsEntityMethod()) {
                continue;
            }

            $got = $dto->$getter();
            if (null === $got) {
                continue;
            }

            if ($got instanceof DataTransferObjectInterface) {
                if ($got::getEntityFqn() === $entityFqn && $got->getId() === $entity->getId()) {
                    $setter = 'set' . $propertyName;
                    $dto->$setter($entity);
                    continue;
                }
                $this->replaceNestedDtoWithEntityInstanceIfIdsMatch($got, $entity);
                continue;
            }

            throw new \LogicException('Unexpected got item ' . \get_class($got));
        }
        foreach ($collectionGetters as $getter) {
            $got = $dto->$getter();
            if (false === ($got instanceof Collection)) {
                continue;
            }
            foreach ($got as $key => $gotItem) {
                if (false === ($gotItem instanceof DataTransferObjectInterface)) {
                    continue;
                }
                if ($gotItem::getEntityFqn() === $entityFqn && $gotItem->getId() === $entity->getId()) {
                    $got->set($key, $entity);
                    continue;
                }
                $this->replaceNestedDtoWithEntityInstanceIfIdsMatch($gotItem, $entity);
            }
        }
    }

    private function getGettersForDtosOrCollections(DataTransferObjectInterface $dto): array
    {
        $dtoReflection     = new ReflectionClass(\get_class($dto));
        $dtoGetters        = [];
        $collectionGetters = [];
        foreach ($dtoReflection->getMethods() as $method) {
            $methodName = $method->getName();
            if (0 !== strpos($methodName, 'get')) {
                continue;
            }
            $returnType = $method->getReturnType();
            if (null === $returnType) {
                continue;
            }
            $returnTypeName = $returnType->getName();
            if (false === \ts\stringContains($returnTypeName, '\\')) {
                continue;
            }
            $returnTypeReflection = new ReflectionClass($returnTypeName);

            if ($returnTypeReflection->implementsInterface(DataTransferObjectInterface::class)) {
                $dtoGetters[] = $methodName;
                continue;
            }
            if ($returnTypeReflection->implementsInterface(Collection::class)) {
                $collectionGetters[] = $methodName;
                continue;
            }
        }

        return [$dtoGetters, $collectionGetters];
    }

    private function replaceNestedDtosWithNewEntities(DataTransferObjectInterface $dto)
    {
        $getters = $this->getGettersForDtosOrCollections($dto);
        if ([[], []] === $getters) {
            return;
        }
        list($dtoGetters, $collectionGetters) = array_values($getters);
        foreach ($dtoGetters as $getter) {
            $propertyName        = substr($getter, 3, -3);
            $issetAsEntityMethod = 'isset' . $propertyName . 'AsEntity';
            if (true === $dto->$issetAsEntityMethod()) {
                continue;
            }

            $nestedDto = $dto->$getter();
            if (null === $nestedDto) {
                continue;
            }
            $setter = 'set' . substr($getter, 3, -3);
            $dto->$setter($this->createEntity($nestedDto::getEntityFqn(), $nestedDto, false));
        }
        foreach ($collectionGetters as $getter) {
            $nestedDto = $dto->$getter();
            if (false === ($nestedDto instanceof Collection)) {
                continue;
            }
            $this->convertCollectionOfDtosToEntities($nestedDto);
        }
    }

    /**
     * This will take an ArrayCollection of DTO objects and replace them with the Entities
     *
     * @param Collection $collection
     *
     * @throws \ReflectionException
     */
    private function convertCollectionOfDtosToEntities(Collection $collection)
    {
        if (0 === $collection->count()) {
            return;
        }
        $dtoFqn              = null;
        $collectionEntityFqn = null;
        foreach ($collection as $dto) {
            if ($dto instanceof EntityInterface) {
                $collectionEntityFqn = \get_class($dto);
                break;
            }
            if (null === $dtoFqn) {
                $dtoFqn = \get_class($dto);
                continue;
            }
            if (false === ($dto instanceof $dtoFqn)) {
                throw new \InvalidArgumentException(
                    'Mismatched collection, expecting dtoFqn ' .
                    $dtoFqn .
                    ' but found ' .
                    \get_class($dto)
                );
            }
        }
        if (null === $collectionEntityFqn) {
            $collectionEntityFqn = $this->namespaceHelper->getEntityFqnFromEntityDtoFqn($dtoFqn);
        }
        foreach ($collection as $key => $dto) {
            if ($dto instanceof $collectionEntityFqn) {
                continue;
            }
            if (false === ($dto instanceof DataTransferObjectInterface)) {
                throw new \InvalidArgumentException('Found none DTO item in collection, was instance of ' .
                                                    \get_class($dto));
            }
            if (false === ($dto instanceof $dtoFqn)) {
                throw new \InvalidArgumentException('Unexpected DTO ' . \get_class($dto) . ', expected ' . $dtoFqn);
            }
            $collection->set($key, $this->createEntity($collectionEntityFqn, $dto, false));
        }
    }

    /**
     * Loop through all created entities and reset the transaction running property to false,
     * then remove the list of created entities
     */
    private function stopTransaction(): void
    {
        foreach ($this->created as $entity) {
            $transactionProperty = $entity::getDoctrineStaticMeta()
                                          ->getReflectionClass()
                                          ->getProperty(AlwaysValidInterface::TRANSACTION_RUNNING_PROPERTY);
            $transactionProperty->setAccessible(true);
            $transactionProperty->setValue($entity, false);
        }
        $this->created = [];
    }
}
