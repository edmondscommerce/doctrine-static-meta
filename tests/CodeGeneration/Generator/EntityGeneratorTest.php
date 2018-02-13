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
               .'\\'.static::TEST_PROJECT_ENTITIES_FOLDER
               .'\\Yet\\Another\\TestEntity';
        $this->getEntityGenerator()->generateEntity($fqn);
        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.static::TEST_PROJECT_ENTITIES_FOLDER
                       .'/Yet/Another/TestEntity.php';
        $this->assertTemplateCorrect($createdFile);

        $createdFile = static::WORK_DIR
                       .'/'.AbstractCommand::DEFAULT_SRC_SUBFOLDER
                       .'/'.static::TEST_PROJECT_ENTITY_REPOSITORIES_FOLDER
                       .'/Yet/Another/TestEntityRepository.php';
        $this->assertTemplateCorrect($createdFile);
    }
}
