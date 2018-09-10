<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\GenericFactoryInterface;
use ReflectionMethod;

class EntityFactory implements GenericFactoryInterface
{
    public const INJECT_DEPENDENCY_METHOD_PREFIX = 'inject';

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

    /**
     * This array is keyed by Entity FQN and the values are dependencies
     *
     * @var array|object[]
     */
    private $entityDependencies = [];

    /**
     * This array is keyed by Entity FQN and the values are the inject*** method names that are used for injecting
     * dependencies
     *
     * @var array|ReflectionMethod
     */
    private $entityInjectMethods = [];

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
        $this->injectEntityDependencies($entity);
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
     * This method loops over the inject methods for an Entity and then injects the relevant dependencies
     *
     * We match the method argument type with the dependency to be injected. The limitation here is that you can only
     * have one inject method for a set type so the inject method should type hint for something as precise as
     * possible
     *
     * @param EntityInterface $entity
     */
    private function injectEntityDependencies(EntityInterface $entity)
    {
        $methods      = $this->getInjectMethodsForEntity($entity);
        $dependencies = $this->entityDependencies[$entity::getDoctrineStaticMeta()->getReflectionClass()->getName()];
        foreach ($dependencies as $dependency) {
            foreach ($methods as $key => $method) {
                $params = $method->getParameters();
                if (1 !== count($params)) {
                    throw new \RuntimeException(
                        'Invalid method signature for ' .
                        $method->getName() .
                        ', should only take one argument which is the dependency to be injected'
                    );
                }
                $type = current($params)->getType()->getName();
                if ($dependency instanceof $type) {
                    $methodName = $method->getName();
                    $entity->$methodName($dependency);
                    unset($methods[$key]);
                    continue 2;
                }
            }
            throw new \RuntimeException(
                'Failed finding an inject method in ' .
                $entity::getDoctrineStaticMeta()->getShortName() .
                ' for dependency: ' .
                \get_class($dependency)
            );
        }
    }

    /**
     * Build and retrieve the array of inject method names for an Entity
     *
     * Validates that the number of inject methods and the number of dependencies marked for injection matches up
     *
     * @param EntityInterface $entity
     *
     * @return array|ReflectionMethod[]
     */
    private function getInjectMethodsForEntity(EntityInterface $entity): array
    {
        $reflection = $entity::getDoctrineStaticMeta()->getReflectionClass();
        $entityFqn  = $reflection->getName();
        if (array_key_exists($entityFqn, $this->entityInjectMethods)) {
            return $this->entityInjectMethods[$entityFqn];
        }
        $this->entityInjectMethods[$entityFqn] = [];
        $methods                               = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (!\ts\stringStartsWith(self::INJECT_DEPENDENCY_METHOD_PREFIX, $method->getName())) {
                continue;
            }
            $this->entityInjectMethods[$entityFqn][] = $method;
        }
        $numDependeciesForEntity            = count($this->entityDependencies[$entityFqn]);
        $numDependecyInjectMethodsForEntity = count($this->entityInjectMethods[$entityFqn]);
        if ($numDependeciesForEntity !== $numDependecyInjectMethodsForEntity) {
            throw new \RuntimeException('The number of dependencies [' .
                                        $numDependeciesForEntity .
                                        '] and the nubmer of dependency inject methods [' .
                                        $numDependecyInjectMethodsForEntity .
                                        '] does not match.');
        }

        return $this->entityInjectMethods[$entityFqn];
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

    public function addEntityDependency(string $entityFqn, object $dependency)
    {
        $this->entityDependencies[$entityFqn][] = $dependency;
    }
}
