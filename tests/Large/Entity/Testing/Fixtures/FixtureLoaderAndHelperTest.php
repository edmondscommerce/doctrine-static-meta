<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Testing\Fixtures;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\DataFixtures\Loader;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\EnumFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixtureEntitiesModifierInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Schema;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\AbstractEntityFixtureLoader
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\Fixtures\FixturesHelper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FixtureLoaderAndHelperTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            self::TEST_TYPE_LARGE .
                            '/FixturesTest';

    private const ENTITY_WITHOUT_MODIFIER = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Person';

    private const ENTITY_WITH_MODIFIER = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Attributes\\Address';

    protected static $buildOnce = true;
    /**
     * @var FixturesHelper
     */
    private $helper;

    public function setup(): void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR, self::TEST_PROJECT_ROOT_NAMESPACE);
            $this->getFieldSetter()
                 ->setEntityHasField(
                     self::ENTITY_WITHOUT_MODIFIER,
                     EnumFieldTrait::class
                 );
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->recreateDtos();
        $cacheDir = $this->copiedWorkDir . '/cache';
        mkdir($cacheDir, 0777, true);
        $this->helper = new FixturesHelper(
            $this->getEntityManager(),
            $this->container->get(Database::class),
            $this->container->get(Schema::class),
            new FilesystemCache($cacheDir)
        );
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
        $actual      = $this->getRepositoryFactory()
                            ->getRepository($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER))
                            ->findAll();
        $actualCount = count($actual);
        self::assertSame(AbstractEntityFixtureLoader::BULK_AMOUNT_TO_GENERATE, $actualCount);

        return $actual;
    }

    private function getUnmodifiedFixture(): AbstractEntityFixtureLoader
    {
        return $this->getFixture($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER));
    }

    private function getFixture(
        string $entityFqn,
        ?FixtureEntitiesModifierInterface $modifier = null
    ): AbstractEntityFixtureLoader {
        $fixtureFqn = $this->getNamespaceHelper()->getFixtureFqnFromEntityFqn($entityFqn);

        return new $fixtureFqn(
            $this->container->get(TestEntityGeneratorFactory::class),
            $this->container->get(EntitySaverFactory::class),
            $this->container->get(NamespaceHelper::class),
            $modifier
        );
    }

    /**
     * @test
     * @large
     * @depends itLoadsAllTheFixturesWithRandomDataByDefault
     *
     * @param array $loadedFirstTime
     *
     * @return array
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itUsesTheCacheTheSecondTime(array $loadedFirstTime): array
    {
        $this->getFileSystem()
             ->mirror(
                 $this->copiedWorkDir .
                 '/../FixtureLoaderAndHelperTest_ItLoadsAllTheFixturesWithRandomDataByDefault_/cache',
                 $this->copiedWorkDir . '/cache'
             );
        $this->helper->setCacheKey(__CLASS__ . '_unmodified');
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        self::assertTrue($this->helper->isLoadedFromCache());
        /**
         * @var EntityInterface[] $loadedSecondTime
         */
        $loadedSecondTime = $this->getRepositoryFactory()
                                 ->getRepository($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER))
                                 ->findAll();
        $actualCount      = count($loadedSecondTime);
        $expectedCount    = count($loadedFirstTime);
        self::assertSame($expectedCount, $actualCount);
        foreach ($loadedSecondTime as $key => $actualEntity) {
            $expectedEntity = $loadedFirstTime[$key];
            $actualId       = $actualEntity->getId();
            $expectedId     = $expectedEntity->getId();
            $expectedText   = $expectedEntity->getString();
            $actualText     = $actualEntity->getString();
            self::assertEquals($expectedId, $actualId, 'Cached Entity ID does not match');
            self::assertEquals($expectedText, $actualText, 'Cached Faker data does not match');
        }

        return $loadedSecondTime;
    }

    /**
     * @test
     * @large
     * @depends itUsesTheCacheTheSecondTime
     *
     * @param array $loadedSecondTime
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function itCanBeConfiguredNotToLoadFromTheCache(array $loadedSecondTime): void
    {
        $this->getFileSystem()
             ->mirror(
                 $this->copiedWorkDir .
                 '/../FixtureLoaderAndHelperTest_ItUsesTheCacheTheSecondTime_/cache',
                 $this->copiedWorkDir . '/cache'
             );
        $this->helper->setCacheKey(__CLASS__ . '_unmodified');
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->setLoadFromCache(false);
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        self::assertFalse($this->helper->isLoadedFromCache());
        /**
         * @var EntityInterface[] $loadedThirdTime
         */
        $loadedThirdTime = $this->getRepositoryFactory()
                                ->getRepository($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER))
                                ->findAll();
        $actualCount     = count($loadedThirdTime);
        $expectedCount   = count($loadedSecondTime);
        self::assertSame($expectedCount, $actualCount);
        foreach ($loadedThirdTime as $key => $actualEntity) {
            $loadedSecondTimeEntity = $loadedSecondTime[$key];
            $actualId               = $actualEntity->getId();
            $secondTimeEntityId     = $loadedSecondTimeEntity->getId();
            $secondTimeText         = $loadedSecondTimeEntity->getString();
            $actualText             = $actualEntity->getString();
            self::assertNotEquals($secondTimeEntityId, $actualId, 'Cached Entity ID matches, this should not happen');
            self::assertNotEquals($secondTimeText, $actualText, 'Cached Faker data matches, this should not happen');
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
        $actual      = $this->getRepositoryFactory()
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
            $this->getEntityFactory(),
            $this->getEntityDtoFactory()
        )
            implements FixtureEntitiesModifierInterface
        {
            /**
             * @var string
             */
            protected $entityFqn;
            /**
             * @var EntityFactoryInterface
             */
            protected $factory;
            /**
             * @var array|EntityInterface[]
             */
            private $entities;
            /**
             * @var DtoFactory
             */
            private $dtoFactory;

            public function __construct(string $entityFqn, EntityFactoryInterface $factory, DtoFactory $dtoFactory)
            {
                $this->entityFqn  = $entityFqn;
                $this->factory    = $factory;
                $this->dtoFactory = $dtoFactory;
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
                $this->entities[0]->update(
                    new class($this->entityFqn, $this->entities[0]->getId())
                        implements DataTransferObjectInterface
                    {
                        /**
                         * @var string
                         */
                        private static $entityFqn;
                        /**
                         * @var UuidInterface
                         */
                        private $id;

                        public function __construct(string $entityFqn, UuidInterface $id)
                        {
                            self::$entityFqn = $entityFqn;
                            $this->id        = $id;
                        }

                        public function getString(): string
                        {
                            return 'This has been overridden';
                        }

                        public static function getEntityFqn(): string
                        {
                            return self::$entityFqn;
                        }

                        public function getId(): UuidInterface
                        {
                            return $this->id;
                        }
                    });
            }

            private function addAnotherEntity(): void
            {
                $entity = $this->factory->create(
                    $this->entityFqn,
                    new class implements DataTransferObjectInterface
                    {
                        /**
                         * @var string
                         */
                        private static $entityFqn;
                        /**
                         * @var \Ramsey\Uuid\UuidInterface
                         */
                        private $id;

                        public function __construct(string $entityFqn)
                        {
                            self::$entityFqn = $entityFqn;
                            $this->id        = Uuid::uuid4();
                        }

                        public function getString(): string
                        {
                            return 'This has been created';
                        }

                        public static function getEntityFqn(): string
                        {
                            return self::$entityFqn;
                        }

                        public function getId(): UuidInterface
                        {
                            return $this->id;
                        }
                    }
                );

                $this->entities[] = $entity;
            }
        };
    }

    /**
     * @test
     * @large
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

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function fixturesUseTheCorrectFakerDataProviders(): void
    {
        $entityFqn = $this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER);

        $this->helper->setCacheKey(__CLASS__ . '_faker');
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        $actual = $this->getRepositoryFactory()
                       ->getRepository($entityFqn)
                       ->findAll();
        /**
         * @var EntityInterface $entity
         */
        foreach ($actual as $entity) {
            self::assertContains($entity->getEnum(), EnumFieldInterface::ENUM_OPTIONS);
        }
    }
}
