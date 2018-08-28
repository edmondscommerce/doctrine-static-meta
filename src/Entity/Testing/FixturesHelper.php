<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;

/**
 * To be used in your Test classes. This provides you with the methods to use in your setup method to create the
 * fixtures are required
 */
class FixturesHelper
{
    /**
     * @var Database
     */
    protected $database;
    /**
     * @var ORMExecutor
     */
    private $fixtureExecutor;

    /**
     * @var Loader
     */
    private $fixtureLoader;

    public function __construct(EntityManagerInterface $entityManager, Database $database)
    {
        $purger                = null;
        $this->fixtureExecutor = new ORMExecutor($entityManager, $purger);
        $this->fixtureLoader   = new Loader();
        $this->database        = $database;
    }

    public function addFixture(FixtureInterface $fixture): void
    {
        $this->fixtureLoader->addFixture($fixture);
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
        if (!$this->fixtureExecutor instanceof ORMExecutor) {
            throw new \RuntimeException('You need to call setupFixtureExecutor before you can create fixtures');
        }
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
        $this->fixtureExecutor->execute($this->fixtureLoader->getFixtures(), true);
    }
}
