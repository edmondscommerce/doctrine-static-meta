<?php declare(strict_types=1);

namespace Edmonds\DoctrineStaticMeta\EntityManager;

use Doctrine\ORM\EntityManager;

interface EntityManagerFactoryInterface
{
    public function getEm(): EntityManager;
}
