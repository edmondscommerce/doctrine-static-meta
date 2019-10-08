<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\G\Entity\Validation;

use Doctrine\ORM\Proxy\Proxy;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\Modifiers\AddAssociationEntitiesModifier;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Initialiser;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\GetGeneratedCodeContainerTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Initialiser
 */
class InitialiserTest extends AbstractLargeTest
{
    use GetGeneratedCodeContainerTrait;

    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/InitialiserTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;
    protected static $buildOnce = true;
    /**
     * @var string
     */
    private $testEntityFqn;
    /**
     * @var Initialiser
     */
    private $initialiser;

    public function setUp()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
        $this->testEntityFqn = $this->getCopiedFqn(self::TEST_ENTITY_FQN);
        $this->setupDbWithFixtures();
        $this->initialiser = $this->container->get(Initialiser::class);
    }

    /**
     * Sets up the DB with the Person Fixture
     *
     * uses the AddAssociationEntitiesModifier to ensure that the Person objects have the full data
     *
     * @throws DoctrineStaticMetaException
     */
    private function setupDbWithFixtures(): void
    {
        $fixturesHelper = $this->getFixturesHelper();
        $fixturesHelper->createDb(
            $fixturesHelper->createFixture(
                $this->getNamespaceHelper()->getFixtureFqnFromEntityFqn($this->testEntityFqn),
                new AddAssociationEntitiesModifier($this->getTestEntityGeneratorFactory())
            )
        );
    }

    /**
     * @test
     * @large
     */
    public function itInitialisesAProxy(): void
    {
        $loaded = $this->getRepositoryFactory()->getRepository($this->testEntityFqn)->findOneBy([]);
        /**
         * @var Proxy
         */
        $attributesAddressProxy = $loaded->getAttributesAddress();
        $expected               = false;
        $actual                 = $attributesAddressProxy->__isInitialized__;
        self::assertSame($expected, $actual);

        $this->initialiser->initialise($attributesAddressProxy);
        $expected = true;
        $actual   = $attributesAddressProxy->__isInitialized__;
        self::assertSame($expected, $actual);
    }


    /**
     * @test
     * @large
     */
    public function itInitalisesAnEntityThatHasUninitialisedCollections(): void
    {
        $loaded = $this->getRepositoryFactory()->getRepository($this->testEntityFqn)->findOneBy([]);
        /**
         * @var Proxy
         */
        $attributesAddressProxy = $loaded->getAttributesAddress();
        $expected               = false;
        $actual                 = $attributesAddressProxy->__isInitialized__;
        self::assertSame($expected, $actual);

        $this->initialiser->initialise($loaded);
        $expected = true;
        $actual   = $attributesAddressProxy->__isInitialized__;
        self::assertSame($expected, $actual);
    }
}
