<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Testing;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\AbstractEntityFixtureLoader;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\FixtureEntitiesModifierInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\FixturesHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\FullProjectBuildLargeTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\AbstractEntityFixtureLoader
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\FixturesHelper
 */
class FixturesTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            self::TEST_TYPE_LARGE .
                            '/TestEntityGeneratorLargeTest';

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
        $this->helper = new FixturesHelper($this->getEntityManager(), $this->container->get(Database::class));
    }

    private function getUnmodifiedFixture(): AbstractEntityFixtureLoader
    {
        $fixtureFqn = $this->getNamespaceHelper()->getFixtureFqnFromEntityFqn(
            $this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER)
        );
        /**
         * @var $fixture AbstractEntityFixtureLoader
         */
        $fixture = new $fixtureFqn(
            $this->container->get(TestEntityGenerator::class),
            $this->container->get(EntitySaverFactory::class)
        );

        return $fixture;
    }

    private function getModifiedFixture(): AbstractEntityFixtureLoader
    {
        $fixtureFqn = $this->getNamespaceHelper()->getFixtureFqnFromEntityFqn(
            $this->getCopiedFqn(self::ENTITY_WITH_MODIFIER)
        );
        /**
         * @var $fixture AbstractEntityFixtureLoader
         */
        $fixture = new $fixtureFqn(
            $this->container->get(TestEntityGenerator::class),
            $this->container->get(EntitySaverFactory::class),
            new class(self::ENTITY_WITH_MODIFIER)
                implements FixtureEntitiesModifierInterface
            {
                /**
                 * @var string
                 */
                protected $entityFqn;
                /**
                 * @var array|EntityInterface[]
                 */
                private $entities;

                public function __construct(string $entityFqn)
                {
                    $this->entityFqn = $entityFqn;
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
                    $entity = new $this->entityFqn();
                    $entity->setString('This has been created');
                    $this->entities[] = $entity;
                }
            }
        );

        return $fixture;

    }

    /**
     * @test
     * @large
     */
    public function itLoadsAllTheFixturesWithRandomDataByDefault(): void
    {
        $fixture = $this->getUnmodifiedFixture();
        $this->helper->addFixture($fixture);
        $this->helper->createDb();
        $actual      = $this->getEntityManager()->getRepository($this->getCopiedFqn(self::ENTITY_WITHOUT_MODIFIER))->findAll();
        $actualCount = count($actual);
        self::assertSame(AbstractEntityFixtureLoader::BULK_AMOUNT_TO_GENERATE, $actualCount);
    }

    /**
     * @test
     * @large
     */
    public function itCanTakeAModifierToCustomiseTheFixtures()
    {

    }
}
