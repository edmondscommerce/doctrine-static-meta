<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;

class EntityGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/EntityGeneratorTest/';

    /**
     */
    public function testGenerateEntity()
    {
        $fqn = static::TEST_PROJECT_ROOT_NAMESPACE
               .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME
               .'\\Yet\\Another\\TestEntity';
        $this->getEntityGenerator()->generateEntity($fqn);
        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.AbstractGenerator::ENTITIES_FOLDER_NAME
                       .'/Yet/Another/TestEntity.php';
        $this->assertTemplateCorrect($createdFile);

        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                       .'/Yet/Another/TestEntityRepository.php';
        $this->assertTemplateCorrect($createdFile);
    }

    public function testGenerateEntityWithEntitiesInProjectName()
    {
        $projectRootNamespace = 'My\\TestEntities\\Project';
        $fqn                  = $projectRootNamespace.'\\Entities\\Cheese';
        $this->getEntityGenerator()->setProjectRootNamespace($projectRootNamespace)->generateEntity($fqn);

        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.AbstractGenerator::ENTITIES_FOLDER_NAME
                       .'/Cheese.php';
        $this->assertTemplateCorrect($createdFile);

        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                       .'/CheeseRepository.php';
        $this->assertTemplateCorrect($createdFile);

    }
}
