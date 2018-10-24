<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\PostProcessor;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\EntityFormatter;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use ts\Reflection\ReflectionClass;

class EntityFormatterTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/EntityFormatterTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;

    private const ENTITY_FORMATTED = '';

    public function setUp()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     * @throws \ReflectionException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function itFormatsEntities(): void
    {
        $this->getEntityFormatter()->setPathToProjectRoot($this->copiedWorkDir)->run();
        $expected = self::ENTITY_FORMATTED;
        $actual   = \ts\file_get_contents(
            (new ReflectionClass($this->getCopiedFqn(self::TEST_ENTITY)))->getFileName()
        );
        self::assertSame($expected, $actual);

    }

    private function getEntityFormatter(): EntityFormatter
    {
        return $this->container->get(EntityFormatter::class);
    }
}