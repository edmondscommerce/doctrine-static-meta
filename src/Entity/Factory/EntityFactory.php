<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\GenericFactoryInterface;

class EntityFactory implements GenericFactoryInterface
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
     * @var EntityValidatorFactory
     */
    private $entityValidatorFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(
        EntityValidatorFactory $entityValidatorFactory,
        NamespaceHelper $namespaceHelper,
        EntityDependencyInjector $entityDependencyInjector
    ) {
        $this->entityValidatorFactory   = $entityValidatorFactory;
        $this->namespaceHelper          = $namespaceHelper;
        $this->entityDependencyInjector = $entityDependencyInjector;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): self
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
     * @param string $entityFqn
     *
     * @param array  $values
     *
     * @return mixed
     */
    public function create(string $entityFqn, array $values = [])
    {
        $this->assertEntityManagerSet();

        return $this->createEntity($entityFqn, $values);
    }

    /**
     * Create the Entity
     *
     * @param string $entityFqn
     *
     * @param array  $values
     *
     * @return EntityInterface
     */
    private function createEntity(string $entityFqn, array $values): EntityInterface
    {
        return $entityFqn::create($this, $values);
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
