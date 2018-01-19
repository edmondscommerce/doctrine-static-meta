<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;

class EntityGeneratorTest extends AbstractCodeGenerationTest
{
    public function testGenerateEntity()
    {
        $fqn = static::TEST_NAMESPACE
            . '\\' . static::TEST_PROJECT_ENTITIES_NAMESPACE
            . '\\Yet\\Another\\TestEntity';
        (new EntityGenerator(
            static::TEST_PROJECT_ROOT_NAMESPACE,
            static::WORK_DIR,
            static::TEST_PROJECT_ENTITIES_NAMESPACE
        ))->generateEntity($fqn);
        $createdFile = static::WORK_DIR
            . '/' . AbstractCommand::DEFAULT_SRC_SUBFOLDER
            . '/' . static::TEST_PROJECT_ENTITIES_NAMESPACE
            . '/Yet/Another/TestEntity.php';
        $this->assertTemplateCorrect($createdFile);
    }
}
