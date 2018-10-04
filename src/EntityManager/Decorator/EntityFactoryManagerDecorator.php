<?php

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Decorator;

use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\EntityFactoryAware;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\GenericFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

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

    public function getRepository($className)
    {
        throw new DoctrineStaticMetaException(
            'You must not use the Entity manager to get your repository, '
            . 'please type hint and dependency inject it as required'
        );
    }
}
