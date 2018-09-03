<?php

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\NamingStrategy;

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
            return $this->genericFactory->getEntity($this->name);
        }

        return parent::newInstance();
    }

    public function setFactories(array $entityFactories, GenericFactoryInterface $genericFactory = null): void
    {
        $this->entityFactories = $entityFactories;
        $this->genericFactory  = $genericFactory;
    }
}
