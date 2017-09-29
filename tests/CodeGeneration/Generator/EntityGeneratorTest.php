<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;

class EntityGeneratorTest extends AbstractCodeGenerationTest
{
    public function testGenerateEntity()
    {
        $fqn = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
            .self::TEST_PROJECT_ENTITIES_NAMESPACE
            .'\\Yet\\Another\\TestEntity';
        (new EntityGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        ))->generateEntity($fqn);
        $createdFile = self::WORK_DIR.'/'.self::TEST_PROJECT_ENTITIES_NAMESPACE.'/Yet/Another/TestEntity.php';
        $this->assertTemplateCorrect($createdFile);
    }
}
