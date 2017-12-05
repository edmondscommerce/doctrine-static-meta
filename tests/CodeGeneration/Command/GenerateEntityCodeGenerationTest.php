<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateEntityCodeGenerationTest extends AbstractCodeGenerationTest
{

    public function testGenerateEntity()
    {
        $application = new Application();
        $helperSet = require __DIR__ . '/../../../cli-config.php';
        $application->setHelperSet($helperSet);
        $command = new GenerateEntityCommand();
        $application->add($command);
        $tester = new CommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . GenerateEntityCommand::OPT_FQN_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                    . self::TEST_PROJECT_ENTITIES_NAMESPACE . '\\This\\Is\\A\\TestEntity',
            ]
        );
        $createdFile = $this->entitiesPath . '/This/Is/A/TestEntity.php';
        $this->assertTemplateCorrect($createdFile);
    }
}
