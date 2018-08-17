<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Testing;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class EntityDebugDumperTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Testing
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper
 */
class EntityDebugDumperTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE . '/ContainerTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\TestEntity';

    private const TEST_DECIMAL_FIELD = self::TEST_PROJECT_ROOT_NAMESPACE
                                       . '\\Entity\\Fields\\Traits\\DecimalFieldTrait';

    private const VALUE_DECIMAL = '20.10000000000000';

    /**
     * @var EntityDebugDumper
     */
    private static $dumper;

    public static function setUpBeforeClass()
    {
        self::$dumper = new EntityDebugDumper();
    }

    public function setup()
    {
        parent::setup();
        $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_FQN);
        $this->getFieldGenerator()->generateField(
            self::TEST_DECIMAL_FIELD,
            MappingHelper::TYPE_DECIMAL
        );
        $this->getFieldSetter()->setEntityHasField(
            self::TEST_ENTITY_FQN,
            self::TEST_DECIMAL_FIELD
        );
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     * @medium
     * @covers ::dump
     */
    public function itRemovesTrailingZerosOnDecimals(): void
    {
        self::assertNotContains(self::VALUE_DECIMAL, self::$dumper->dump($this->getEntity()));
    }

    private function getEntity(): EntityInterface
    {
        $entity = $this->createEntity($this->getCopiedFqn(self::TEST_ENTITY_FQN));
        $entity->setDecimal(self::VALUE_DECIMAL);

        return $entity;
    }
}
