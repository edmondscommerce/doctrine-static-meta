<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\EntityManager;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;

interface EntityManagerFactoryInterface
{
    public function getEntityManager(
        ConfigInterface $config
    ): EntityManagerInterface;
}
