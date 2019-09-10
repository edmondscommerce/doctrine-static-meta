<?php

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\NamingStrategy;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface as GenericFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use RuntimeException;

class ClassMetadataWithEntityFactories extends ClassMetadata
{
    /** @var EntityFactoryInterface[] */
    private $entityFactories;
    /** @var GenericFactoryInterface|null */
    private $genericFactory;

    public function __construct(
        $entityName,
        NamingStrategy $namingStrategy = null,
        array $entityFactories = [],
        GenericFactoryInterface $genericFactory = null
    ) {
        parent::__construct($entityName, $namingStrategy);
        $this->entityFactories = $entityFactories;
        $this->genericFactory  = $genericFactory;
    }

    public function newInstance()
    {
        if (isset($this->entityFactories[$this->name])) {
            return $this->entityFactories[$this->name]->getEntity();
        }

        if ($this->genericFactory !== null) {
            $entity = parent::newInstance();
            if (!$entity instanceof EntityInterface) {
                throw new RuntimeException('Expected Entity Interface, got ' . get_class($entity));
            }
            $this->genericFactory->initialiseEntity($entity);

            return $entity;
        }

        return parent::newInstance();
    }

    public function setFactories(array $entityFactories, GenericFactoryInterface $genericFactory = null): void
    {
        $this->entityFactories = $entityFactories;
        $this->genericFactory  = $genericFactory;
    }
}
