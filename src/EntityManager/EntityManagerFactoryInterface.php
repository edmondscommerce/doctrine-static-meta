<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\SchemaBuilder;

interface EntityManagerFactoryInterface
{
    public static function getEntityManager(
        ConfigInterface $config,
        ?SchemaBuilder $schemaBuilder = null
    ): EntityManager;
}
