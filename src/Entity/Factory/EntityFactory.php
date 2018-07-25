<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;

class EntityFactory
{
    /**
     * @var EntityValidatorFactory
     */
    protected $entityValidatorFactory;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityValidatorFactory $entityValidatorFactory, EntityManagerInterface $entityManager)
    {
        $this->entityValidatorFactory = $entityValidatorFactory;
        $this->entityManager          = $entityManager;
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
        $entity = $this->createEntity($entityFqn);
        $entity->ensureMetaDataIsSet($this->entityManager);
        $this->setEntityValues($entity, $values);

        return $entity;
    }

    private function createEntity(string $entityFqn): EntityInterface
    {
        return new $entityFqn($this->entityValidatorFactory);
    }

    private function setEntityValues(EntityInterface $entity, array $values): void
    {
        if ([] === $values) {
            return;
        }
        foreach ($values as $property => $value) {
            $setter = 'set'.$property;
            if (!method_exists($entity, $setter)) {
                throw new \InvalidArgumentException(
                    'The entity '.$entityFqn.' does not have the setter method '.$setter
                );
            }
            $entity->$setter($value);
        }

        return;
    }
}
