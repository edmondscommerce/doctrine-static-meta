<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
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


    public function __construct(
        NamespaceHelper $namespaceHelper,
        EntityDependencyInjector $entityDependencyInjector
    ) {
        $this->namespaceHelper          = $namespaceHelper;
        $this->entityDependencyInjector = $entityDependencyInjector;
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
     * Build a new entity with the validator factory preloaded
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

        return $this->createEntity($entityFqn, $dto);
    }

    /**
     * Create the Entity
     *
     * @param string                           $entityFqn
     *
     * @param DataTransferObjectInterface|null $dto
     *
     * @return EntityInterface
     */
    private function createEntity(string $entityFqn, DataTransferObjectInterface $dto = null): EntityInterface
    {
        $this->replaceNestedDtosWithNewEntities($dto);

        return $entityFqn::create($this, $dto);
    }

    private function replaceNestedDtosWithNewEntities(?DataTransferObjectInterface $dto)
    {
        if (null === $dto) {
            return;
        }

        $getters = $this->getGettersForDtosOrCollections($dto);
        if ([] === $getters) {
            return;
        }
        foreach ($getters as $getter) {
            $nestedDto = $dto->$getter();
            if ($nestedDto instanceof Collection) {
                $this->convertArrayCollectionOfDtosToEntities($nestedDto, $collectionEntityFqn);
                continue;
            }
            if (false === ($nestedDto instanceof DataTransferObjectInterface)) {
                continue;
            }
            $setter          = 'set' . substr($getter, 3, -3);
            $nestedEntityFqn = $this->namespaceHelper->getEntityFqnFromEntityDtoFqn(\get_class($nestedDto));
            $dto->$setter($this->create($nestedEntityFqn, $nestedDto));
        }
    }

    private function getGettersForDtosOrCollections(DataTransferObjectInterface $dto): array
    {
        $dtoReflection = new ReflectionClass(\get_class($dto));
        $return        = [];
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
                $return[] = $methodName;
                continue;
            }
            if ($returnTypeReflection->implementsInterface(Collection::class)) {
                $return[] = $methodName;
                continue;
            }
        }

        return $return;
    }

    /**
     * This will take an ArrayCollection of DTO objects and replace them with the Entities
     *
     * @param Collection $collection
     * @param string     $collectionEntityFqn
     */
    private function convertArrayCollectionOfDtosToEntities(Collection $collection, string $collectionEntityFqn)
    {
        $dtoFqn = null;
        foreach ($collection as $key => $dto) {
            if ($dto instanceof $collectionEntityFqn) {
                continue;
            }
            if (false === ($dto instanceof DataTransferObjectInterface)) {
                throw new \InvalidArgumentException('Found none DTO item in collection, was instance of ' .
                                                    \get_class($dto));
            }
            if (null === $dtoFqn) {
                $dtoFqn = \get_class($dto);
            }
            if (false === ($dto instanceof $dtoFqn)) {
                throw new \InvalidArgumentException('Unexpected DTO ' . \get_class($dto) . ', expected ' . $dtoFqn);
            }
            $collection->set($key, $this->create($collectionEntityFqn, $dto));
        }
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
}
