<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateEntityCommandTest extends AbstractCommandTest
{

    public function testGenerateEntity()
    {
        $command = new GenerateEntityCommand();
        $tester = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . GenerateEntityCommand::OPT_FQN_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                    . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\This\\Is\\A\\TestEntity',
            ]
        );
        $createdFiles = [
            $this->entitiesPath . '/This/Is/A/TestEntity.php',
            $this->entitiesPath . '/../../tests/Entities/This/Is/A/TestEntity.php'
        ];
        foreach ($createdFiles as $createdFile) {
            $this->assertTemplateCorrect($createdFile);
        }

    }
}
