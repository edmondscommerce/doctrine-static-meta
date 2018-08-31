<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Testing;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\DataFixtures\Loader;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixtureEntitiesModifierInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\FullProjectBuildLargeTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FixturesTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            self::TEST_TYPE_LARGE .
                            '/FixturesTest';

    private const TEST_ENTITIES = FullProjectBuildLargeTest::TEST_ENTITIES;

    private const TEST_RELATIONS = FullProjectBuildLargeTest::TEST_RELATIONS;

    private const TEST_FIELD_FQN_BASE = FullProjectBuildLargeTest::TEST_FIELD_NAMESPACE_BASE . '\\Traits';

    private const ENTITY_WITHOUT_MODIFIER = self::TEST_ENTITIES[0];

    private const ENTITY_WITH_MODIFIER = self::TEST_ENTITIES[1];

    protected static $buildOnce = true;
    /**
     * @var FixturesHelper
     */
    private $helper;

    public function setup(): void
    {
        parent::setup();
        if (false === self::$built) {
            $entityGenerator    = $this->getEntityGenerator();
            $fieldGenerator     = $this->getFieldGenerator();
            $relationsGenerator = $this->getRelationsGenerator();
            $fields             = [];
            foreach (MappingHelper::COMMON_TYPES as $type) {
                $fields[] = $fieldGenerator->generateField(
                    self::TEST_FIELD_FQN_BASE . '\\' . ucwords($type),
                    $type
                );
            }
            foreach (self::TEST_ENTITIES as $entityFqn) {
                $entityGenerator->generateEntity($entityFqn);
                foreach ($fields as $fieldFqn) {
                    $this->getFieldSetter()->setEntityHasField($entityFqn, $fieldFqn);
                }
            }
            foreach (self::TEST_RELATIONS as $relation) {
                $relationsGenerator->setEntityHasRelationToEntity(...$relation);
            }

            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->helper = new FixturesHelper(
            $this->getEntityManager(),
            $this->container->get(Database::class),
            $this->container->get(Schema::class),
            $this->container->get(FilesystemCache::class)
        );
    }

    private function getFixture(
        string $entityFqn,
        ?FixtureEntitiesModifierInterface $modifier = null
    ): AbstractEntityFixtureLoader {
        $fixtureFqn = $this->getNamespaceHelper()->getFixtureFqnFromEntityFqn($entityFqn);

        return new $fixtureFqn(
            $this->container->get(TestEntityGeneratorFactory::class),
            $this->container->get(EntitySaverFactory::class),
            $modifier
        );
    }

    private function getUnmodifiedFixture(): AbstractEntityFixtureLoader
    {
        return $this->getFixture($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER));
    }

    private function getModifiedFixture(): AbstractEntityFixtureLoader
    {
        return $this->getFixture(
            $this->getCopiedFqn(self::ENTITY_WITH_MODIFIER),
            $this->getFixtureModifier()
        );
    }

    private function getFixtureModifier(): FixtureEntitiesModifierInterface
    {
        return new class(
            $this->getCopiedFqn(self::ENTITY_WITH_MODIFIER),
            $this->getEntityFactory()
        )
            implements FixtureEntitiesModifierInterface
        {
            /**
             * @var string
             */
            protected $entityFqn;
            /**
             * @var EntityFactory
             */
            protected $factory;
            /**
             * @var array|EntityInterface[]
             */
            private $entities;

            public function __construct(string $entityFqn, EntityFactory $factory)
            {
                $this->entityFqn = $entityFqn;
                $this->factory   = $factory;
            }

            /**
             * Update the entities array by reference
             *
             * @param array $entities
             */
            public function modifyEntities(array &$entities): void
            {
                $this->entities = &$entities;
                $this->updateFirstEntity();
                $this->addAnotherEntity();
            }

            private function updateFirstEntity(): void
            {
                $this->entities[0]->setString('This has been overridden');
            }

            private function addAnotherEntity(): void
            {
                $entity = $this->factory->create($this->entityFqn);
                $entity->setString('This has been created');
                $this->entities[] = $entity;
            }
        };
    }

    /**
     * @test
     * @large
     */
    public function itLoadsAllTheFixturesWithRandomDataByDefault(): array
    {
        $this->helper->setCacheKey(__CLASS__ . '_unmodified');
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        $actual      = $this->getEntityManager()
                            ->getRepository($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER))
                            ->findAll();
        $actualCount = count($actual);
        self::assertSame(AbstractEntityFixtureLoader::BULK_AMOUNT_TO_GENERATE, $actualCount);

        return $actual;
    }

    /**
     * @test
     * @large
     * @depends itLoadsAllTheFixturesWithRandomDataByDefault
     *
     * @param array $loadedFirstTime
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itUsesTheCacheTheSecondTime(array $loadedFirstTime): void
    {
        $this->helper->setCacheKey(__CLASS__ . '_unmodified');
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        self::assertTrue($this->helper->isLoadedFromCache());
        /**
         * @var EntityInterface[] $actual
         */
        $actual        = $this->getEntityManager()
                              ->getRepository($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER))
                              ->findAll();
        $actualCount   = count($actual);
        $expectedCount = count($loadedFirstTime);
        self::assertSame($expectedCount, $actualCount);
        foreach ($actual as $key => $actualEntity) {
            $expectedEntity = $loadedFirstTime[$key];
            $actualId       = $actualEntity->getId();
            $expectedId     = $expectedEntity->getId();
            $expectedText   = $expectedEntity->getString();
            $actualText     = $actualEntity->getString();
            self::assertEquals($expectedId, $actualId, 'Cached Entity ID does not match');
            self::assertEquals($expectedText, $actualText, 'Cached Faker data does not match');
        }
    }

    /**
     * @test
     * @large
     */
    public function itCanTakeAModifierToCustomiseTheFixtures()
    {
        $this->helper->setCacheKey(__CLASS__ . '_modified');
        $fixture = $this->getModifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        /**
         * @var EntityInterface[] $actual
         */
        $actual      = $this->getEntityManager()
                            ->getRepository($this->getCopiedFqn(self::ENTITY_WITH_MODIFIER))
                            ->findAll();
        $actualCount = count($actual);
        self::assertSame(AbstractEntityFixtureLoader::BULK_AMOUNT_TO_GENERATE + 1, $actualCount);
        $firstEntity    = $actual[0];
        $expectedString = 'This has been overridden';
        $actualString   = $firstEntity->getString();
        self::assertSame($expectedString, $actualString);
        end($actual);
        $lastEntity     = current($actual);
        $expectedString = 'This has been created';
        $actualString   = $lastEntity->getString();
        self::assertSame($expectedString, $actualString);
    }

    /**
     * @test
     * @medium
     */
    public function theOrderOfFixtureLoadingCanBeSet(): void
    {
        $loader   = new Loader();
        $fixture1 = $this->getModifiedFixture();
        $loader->addFixture($fixture1);
        $fixture2 = $this->getUnmodifiedFixture();
        $fixture2->setOrder(AbstractEntityFixtureLoader::ORDER_FIRST);
        $loader->addFixture($fixture2);
        $orderedFixtures = $loader->getFixtures();
        self::assertSame($fixture2, current($orderedFixtures));
    }
}
