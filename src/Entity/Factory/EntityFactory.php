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
     * @var EntityValidatorFactory
     */
    private $entityValidatorFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityValidatorFactory $entityValidatorFactory, NamespaceHelper $namespaceHelper)
    {
        $this->entityValidatorFactory = $entityValidatorFactory;
        $this->namespaceHelper        = $namespaceHelper;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
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

    private function assertEntityManagerSet()
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
        $entity = $this->createEntity($entityFqn);
        $this->initialiseEntity($entity, $values);

        return $entity;
    }

    /**
     * Create the Entity
     *
     * @param string $entityFqn
     *
     * @return EntityInterface
     */
    private function createEntity(string $entityFqn): EntityInterface
    {
        return new $entityFqn($this->entityValidatorFactory);
    }

    /**
     * Take an already instantiated Entity and perform the final initialisation steps
     *
     * @param EntityInterface $entity
     * @param array           $values
     */
    public function initialiseEntity(EntityInterface $entity, array $values = []): void
    {
        $entity->ensureMetaDataIsSet($this->entityManager);
        $this->addListenerToEntityIfRequired($entity);
        $this->setEntityValues($entity, $values);
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

    /**
     * Set all the values, if there are any
     *
     * @param EntityInterface $entity
     * @param array           $values
     */
    private function setEntityValues(EntityInterface $entity, array $values): void
    {
        if ([] === $values) {
            return;
        }
        foreach ($values as $property => $value) {
            $setter = 'set' . $property;
            if (!method_exists($entity, $setter)) {
                throw new \InvalidArgumentException(
                    'The entity ' . \get_class($entity) . ' does not have the setter method ' . $setter
                    . "\n\nmethods: " . \print_r(get_class_methods($entity), true)
                );
            }
            $entity->$setter($value);
        }
    }
}
