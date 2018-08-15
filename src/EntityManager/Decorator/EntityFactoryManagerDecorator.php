<?php
namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Decorator;

use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\EntityFactoryAware;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\GenericFactoryInterface;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;

class EntityFactoryManagerDecorator extends EntityManagerDecorator implements EntityFactoryAware
{
    public function __construct(EntityManagerInterface $wrapped)
    {
        parent::__construct($wrapped);
    }

    public function addEntityFactory(string $name, EntityFactoryInterface $entityFactory): void
    {
        $metadataFactory = $this->wrapped->getMetadataFactory();
        if ($metadataFactory instanceof EntityFactoryAware) {
            $metadataFactory->addEntityFactory($name, $entityFactory);
        }
    }

    public function addGenericFactory(GenericFactoryInterface $genericFactory): void
    {
        $metadataFactory = $this->wrapped->getMetadataFactory();
        if ($metadataFactory instanceof EntityFactoryAware) {
            $metadataFactory->addGenericFactory($genericFactory);
        }
    }
}
