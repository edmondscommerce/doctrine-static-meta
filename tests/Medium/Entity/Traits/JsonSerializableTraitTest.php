<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @medium
 */
class JsonSerializableTraitTest extends AbstractTest
{
    public const  WORK_DIR        = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/JsonSerializableTraitTest';
    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                    TestCodeGenerator::TEST_ENTITY_ALL_ARCHETYPE_FIELDS;
    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ErrorException
     * @throws \ReflectionException
     */
    public function entitiesCanBeJsonSerialised(): void
    {
        $entity     = $this->getTestEntityGeneratorFactory()
                           ->createForEntityFqn($this->getCopiedFqn(self::TEST_ENTITY_FQN))
                           ->generateEntity();
        $serialised = \ts\json_encode($entity);
        self::assertNotEmpty($serialised);
        $decoded = json_decode($serialised, true);
        self::assertNotEmpty($decoded);
        self::assertArrayHasKey('id', $decoded);
        self::assertCount(24, $decoded);

    }
}
