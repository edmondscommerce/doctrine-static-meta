<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\GenericFactoryInterface;

class EntityFactory implements GenericFactoryInterface
{
    /**
     * @var EntityValidatorFactory
     */
    private $entityValidatorFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityValidatorFactory $entityValidatorFactory)
    {
        $this->entityValidatorFactory = $entityValidatorFactory;

    }

    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        if (!$this->entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException(
                'No EntityManager set, this must be set first using setEntityManager()'
            );
        }
        $entity = $this->createEntity($entityFqn);
        $entity->ensureMetaDataIsSet($this->entityManager);
        $this->addListenerToEntityIfRequired($entity);
        $this->setEntityValues($entity, $values);

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
                    . "\n\nmethods: " . \get_class_methods($entity)
                );
            }
            $entity->$setter($value);
        }
    }

    public function getEntity(string $className)
    {
        return $this->create($className);
    }
}
