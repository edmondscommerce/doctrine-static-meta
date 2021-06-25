<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Testing\EntityGenerator;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory
 */
class TestEntityGeneratorFactoryTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/TestEntityGeneratorFactoryTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE
                                    . '\\Entities\\TestEntityGeneratorFactoryTestEntity';

    protected static bool $buildOnce = true;

    public function setup():void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY_FQN);
            self::$built = true;
        }
    }

    /**
     * @test
     * @medium
     */
    public function itCanCreateFromAnFqn(): void
    {
        $actual = $this->getTestEntityGeneratorFactory()->createForEntityFqn(self::TEST_ENTITY_FQN);
        self::assertInstanceOf(TestEntityGenerator::class, $actual);
    }
}
