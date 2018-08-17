<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory
 */
class EntitySaverFactoryTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE .
                            '/EntitySaverFactoryTest';

    private const TEST_ENTITIES = [
        'generic'  => self::TEST_PROJECT_ROOT_NAMESPACE
                      . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                      . '\\TestEntity',
        'specific' => self::TEST_PROJECT_ROOT_NAMESPACE
                      . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                      . '\\TestEntitySpecific',
    ];
    protected static $buildOnce = true;
    /**
     * @var EntitySaverFactory
     */
    private $factory;

    public function setup()
    {
        parent::setup();
        $this->factory = new EntitySaverFactory(
            $this->getEntityManager(),
            $this->container->get(EntitySaver::class),
            new NamespaceHelper()
        );
        if (true === self::$built) {
            return;
        }
        $entityGenerator = $this->getEntityGenerator();
        $entityGenerator->generateEntity(self::TEST_ENTITIES['generic']);
        $entityGenerator->generateEntity(self::TEST_ENTITIES['specific'], true);
        self::$built = true;
    }

    /**
     * @test
     * @medium
     * @covers ::getSaverForEntity
     */
    public function getGenericEntitySaver(): void
    {
        $entityFqn = self::TEST_ENTITIES['generic'];
        $entity    = $this->createEntity($entityFqn);
        $actual    = $this->factory->getSaverForEntity($entity);
        self::assertInstanceOf(EntitySaver::class, $actual);
    }

    /**
     * @test
     * @medium
     * @covers ::getSaverForEntity
     */
    public function getSpecificEntitySaver(): void
    {
        $entityFqn = self::TEST_ENTITIES['specific'];
        $entity    = $this->createEntity($entityFqn);
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Savers\\TestEntitySpecificSaver';
        $actual    = $this->factory->getSaverForEntity($entity);
        self::assertInstanceOf($expected, $actual);
    }

    /**
     * @test
     * @medium
     * @covers ::getSaverForEntity
     */
    public function getGenericEntitySaverByFqn(): void
    {
        $entityFqn = self::TEST_ENTITIES['generic'];
        $actual    = $this->factory->getSaverForEntityFqn($entityFqn);
        self::assertInstanceOf(EntitySaver::class, $actual);
    }

    /**
     * @test
     * @medium
     * @covers ::getSaverForEntity
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function getFileSystemetSpecificEntitySaverByFqn(): void
    {
        $entityFqn = self::TEST_ENTITIES['specific'];
        $this->getEntityGenerator()->generateEntity($entityFqn, true);
        $expected = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Savers\\TestEntitySpecificSaver';
        $entity   = $this->createEntity($entityFqn);
        $actual   = $this->factory->getSaverForEntity($entity);
        self::assertInstanceOf($expected, $actual);
    }
}
