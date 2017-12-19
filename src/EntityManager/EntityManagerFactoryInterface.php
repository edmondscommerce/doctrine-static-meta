<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;

interface EntityManagerFactoryInterface
{
    public function getEm(ConfigInterface $config): EntityManager;
}
