<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;

interface EntityManagerFactoryInterface
{
    public static function getEntityManager(
        ConfigInterface $config
    ): EntityManager;
}
