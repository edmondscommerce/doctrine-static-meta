<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;

class EntitySaverFactoryIntegrationTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE .
                            '/EntitySaverFactoryIntegrationTest';

    private const TEST_ENTITIES = [
        'generic'  => self::TEST_PROJECT_ROOT_NAMESPACE
                      . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                      . '\\TestEntity',
        'specific' => self::TEST_PROJECT_ROOT_NAMESPACE
                      . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                      . '\\TestEntitySpecific',
    ];
    /**
     * @var EntitySaverFactory
     */
    private $factory;

    private $built = false;

    public function setup()
    {
        if (true === $this->built) {
            return;
        }
        parent::setup();
        $this->factory   = new EntitySaverFactory(
            $this->getEntityManager(),
            $this->container->get(EntitySaver::class),
            new NamespaceHelper()
        );
        $entityGenerator = $this->getEntityGenerator();
        $entityGenerator->generateEntity(self::TEST_ENTITIES['generic']);
        $entityGenerator->generateEntity(self::TEST_ENTITIES['specific'], true);
    }

    public function testGetGenericEntitySaver(): void
    {
        $entityFqn = self::TEST_ENTITIES['generic'];
        $entity    = $this->createEntity($entityFqn);
        $actual    = $this->factory->getSaverForEntity($entity);
        self::assertInstanceOf(EntitySaver::class, $actual);
    }

    public function testGetSpecificEntitySaver(): void
    {
        $entityFqn = self::TEST_ENTITIES['specific'];
        $entity    = $this->createEntity($entityFqn);
        $expected  = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Savers\\TestEntitySpecificSaver';
        $actual    = $this->factory->getSaverForEntity($entity);
        self::assertInstanceOf($expected, $actual);
    }

    public function testGetGenericEntitySaverByFqn(): void
    {
        $entityFqn = self::TEST_ENTITIES['generic'];
        $actual    = $this->factory->getSaverForEntityFqn($entityFqn);
        self::assertInstanceOf(EntitySaver::class, $actual);
    }

    public function testGetSpecificEntitySaverByFqn(): void
    {
        $entityFqn = self::TEST_ENTITIES['specific'];
        $this->getEntityGenerator()->generateEntity($entityFqn, true);
        $expected = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entity\\Savers\\TestEntitySpecificSaver';
        $entity   = $this->createEntity($entityFqn);
        $actual   = $this->factory->getSaverForEntity($entity);
        self::assertInstanceOf($expected, $actual);
    }
}
