<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class FixturesHelperFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Database
     */
    private $database;
    /**
     * @var Schema
     */
    private $schema;
    /**
     * @var FilesystemAdapter
     */
    private $cache;
    /**
     * @var EntitySaverFactory
     */
    private $entitySaverFactory;
    /**
     * @var NamespaceHelper
     */
    private $namespaceHelper;
    /**
     * @var TestEntityGeneratorFactory
     */
    private $testEntityGeneratorFactory;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        EntityManagerInterface $entityManager,
        Database $database,
        Schema $schema,
        FilesystemAdapter $cache,
        EntitySaverFactory $entitySaverFactory,
        NamespaceHelper $namespaceHelper,
        TestEntityGeneratorFactory $testEntityGeneratorFactory,
        ContainerInterface $container
    ) {
        $this->entityManager              = $entityManager;
        $this->database                   = $database;
        $this->schema                     = $schema;
        $this->cache                      = $cache;
        $this->entitySaverFactory         = $entitySaverFactory;
        $this->namespaceHelper            = $namespaceHelper;
        $this->testEntityGeneratorFactory = $testEntityGeneratorFactory;
        $this->container                  = $container;
    }

    public function getFixturesHelper(string $cacheKey = null): FixturesHelper
    {
        return new FixturesHelper(
            $this->entityManager,
            $this->database,
            $this->schema,
            $this->cache,
            $this->entitySaverFactory,
            $this->namespaceHelper,
            $this->testEntityGeneratorFactory,
            $this->container,
            $cacheKey
        );
    }
}
