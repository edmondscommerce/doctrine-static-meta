<?php

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface as GenericEntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\Mapping\EntityFactoryInterface as EntitySpecificFactory;

interface EntityFactoryAware
{
    public function addEntityFactory(
        string $name,
        EntitySpecificFactory $entityFactory
    ): void;

    public function addGenericFactory(GenericEntityFactoryInterface $genericFactory): void;
}
