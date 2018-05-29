<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use gossi\codegen\model\PhpClass;

class EntitySaverFactoryIntegrationTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/EntitySaverFactoryIntegrationTest';

    private const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE
                                         .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME
                                         .'\\TestEntity';

    public function testGetGenericEntitySaver()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE;
        $factory   = new EntitySaverFactory($this->getEntityManager());
        $this->getEntityGenerator()->generateEntity($entityFqn);
        $entity = new $entityFqn();
        $actual = $factory->getSaverForEntity($entity);
        $this->assertInstanceOf(EntitySaver::class, $actual);
    }

    public function testGetSpecificEntitySaver()
    {
        $entityFqn = self::TEST_ENTITY_FQN_BASE.'Specific';
        $this->getEntityGenerator()->generateEntity($entityFqn);
        $specificEntitySaverFqn = self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entity\\Savers\\TestEntitySpecificSaver';
        $specificEntitySaver    = new PhpClass($specificEntitySaverFqn);
        $specificEntitySaver->setParentClassName('\\'.AbstractEntitySpecificSaver::class);
        $this->getFileSystem()->mkdir(self::WORK_DIR.'/src/Entity/Savers');
        $this->getCodeHelper()->generate(
            $specificEntitySaver,
            self::WORK_DIR.'/src/Entity/Savers/TestEntitySpecificSaver.php'
        );
        $this->setupCopiedWorkDir();
        $factory   = new EntitySaverFactory($this->getEntityManager());
        $entityFqn = $this->getCopiedFqn($entityFqn);
        $entity    = new $entityFqn();
        $actual    = $factory->getSaverForEntity($entity);
        $this->assertInstanceOf($this->getCopiedFqn($specificEntitySaverFqn), $actual);
    }
}
