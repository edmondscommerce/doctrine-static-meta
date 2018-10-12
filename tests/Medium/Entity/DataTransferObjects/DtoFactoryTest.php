<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\DataTransferObjects;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\DtoFactory
 */
class DtoFactoryTest extends AbstractTest
{
    public const  WORK_DIR    = self::VAR_PATH . self::TEST_TYPE_MEDIUM . '/DtoFactoryTest';
    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER;
    protected static $buildOnce = true;
    /**
     * @var DtoFactory
     */
    private static $factory;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
    }

    public static function setUpBeforeClass()
    {
        self::$factory = new DtoFactory(new NamespaceHelper());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function itCanCreateDtoFromEntityFqn(): void
    {
        $actual   = self::$factory->createEmptyDtoFromEntityFqn($this->getTestEntityFqn());
        $expected = $this->getNamespaceHelper()->getEntityDtoFqnFromEntityFqn($this->getTestEntityFqn());
        self::assertInstanceOf($expected, $actual);
    }

    private function getTestEntityFqn(): string
    {
        return $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @throws \ReflectionException
     * @test
     */
    public function itCanCreateDtoFromEntityInstance(): void
    {
        $dto      = self::$factory->createEmptyDtoFromEntityFqn($this->getTestEntityFqn());
        $entity   = $this->createEntity($this->getTestEntityFqn(), $dto);
        $actual   = self::$factory->createDtoFromEntity($entity);
        $expected = $this->getNamespaceHelper()->getEntityDtoFqnFromEntityFqn($this->getTestEntityFqn());
        self::assertInstanceOf($expected, $actual);

    }
}