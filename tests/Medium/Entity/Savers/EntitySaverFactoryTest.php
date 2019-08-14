<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory
 */
class EntitySaverFactoryTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE_MEDIUM .
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
        parent::setUp();
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
     *      */
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
     *      */
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
     *      */
    public function getGenericEntitySaverByFqn(): void
    {
        $entityFqn = self::TEST_ENTITIES['generic'];
        $actual    = $this->factory->getSaverForEntityFqn($entityFqn);
        self::assertInstanceOf(EntitySaver::class, $actual);
    }

    /**
     * @test
     * @medium
     *
     */
    public function getSpecificEntitySaverByFqn(): void
    {
        $entityFqn = self::TEST_ENTITIES['specific'];
        $expected = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Savers\\TestEntitySpecificSaver';
        $entity   = $this->createEntity($entityFqn);
        $actual   = $this->factory->getSaverForEntity($entity);
        self::assertInstanceOf($expected, $actual);
    }
}
