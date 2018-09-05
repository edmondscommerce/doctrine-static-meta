<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;

/**
 * To be used in your Test classes. This provides you with the methods to use in your setup method to create the
 * fixtures are required
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FixturesHelper
{
    /**
     * @var Database
     */
    protected $database;
    /**
     * @var Schema
     */
    protected $schema;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var null|string
     */
    protected $cacheKey;
    /**
     * @var ORMExecutor
     */
    private $fixtureExecutor;

    /**
     * @var Loader
     */
    private $fixtureLoader;

    /**
     * @var bool
     */
    private $loadedFromCache = false;

    public function __construct(
        EntityManagerInterface $entityManager,
        Database $database,
        Schema $schema,
        FilesystemCache $cache,
        ?string $cacheKey = null
    ) {
        $purger                = null;
        $this->fixtureExecutor = new ORMExecutor($entityManager, $purger);
        $this->fixtureLoader   = new Loader();
        $this->database        = $database;
        $this->schema          = $schema;
        $this->entityManager   = $entityManager;
        $this->cache           = $cache;
        $this->cacheKey        = $cacheKey;
    }

    /**
     * @param null|string $cacheKey
     */
    public function setCacheKey(?string $cacheKey): void
    {
        $this->cacheKey = $cacheKey;
    }

    /**
     * Clean the DB and insert fixtures
     *
     * You can pass in a fixture loader directly which will be appended to the main fixtureLoader, or you can add
     * fixtures prior to calling this method
     *
     * We do not use the purge functionality of the `doctrine/data-fixtures` module, instead we fully drop and create
     * the database.
     *
     * @param FixtureInterface $fixture
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function createDb(?FixtureInterface $fixture = null): void
    {
        if (null !== $fixture) {
            $this->addFixture($fixture);
        } elseif ([] === $this->fixtureLoader->getFixtures()) {
            throw new \RuntimeException(
                'No fixtures have been set.'
                . 'You need to either pass in a Fixture to this method, or have called `addFixture` at least once '
                . 'before calling this method'
            );
        }
        $this->database->drop(true)->create(true);
        $cacheKey = $this->getCacheKey();
        if ($this->cache->contains($cacheKey)) {
            $logger = $this->cache->fetch($cacheKey);
            $logger->run($this->entityManager->getConnection());
            $this->loadedFromCache = true;

            return;
        }
        $logger = $this->getLogger();
        $this->entityManager->getConfiguration()->setSQLLogger($logger);
        $this->schema->create();
        $this->fixtureExecutor->execute($this->fixtureLoader->getFixtures(), true);
        $this->entityManager->getConfiguration()->setSQLLogger(null);
        $this->cache->save($cacheKey, $logger);
    }

    public function addFixture(FixtureInterface $fixture): void
    {
        $this->fixtureLoader->addFixture($fixture);
    }

    private function getCacheKey(): string
    {
        if (null !== $this->cacheKey) {
            return $this->cacheKey;
        }

        return md5(print_r(array_keys($this->fixtureLoader->getFixtures()), true));
    }

    private function getLogger(): SQLLogger
    {
        return new Logger();
    }

    /**
     * @return bool
     */
    public function isLoadedFromCache(): bool
    {
        return $this->loadedFromCache;
    }
}
