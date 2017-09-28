<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateEntityCommandTest extends AbstractCommandTest
{

    public function testGenerateEntity()
    {
        $application = new Application();
        $helperSet = require __DIR__.'/../../../cli-config.php';
        $application->setHelperSet($helperSet);
        $command = new GenerateEntityCommand();
        $application->add($command);
        $tester = new CommandTester($command);
        $tester->execute(
            [
                '-'.GenerateEntityCommand::ARG_PROJECT_ROOT_PATH_SHORT => self::WORK_DIR,
                '-'.GenerateEntityCommand::ARG_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-'.GenerateEntityCommand::ARG_FQN_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.self::TEST_PROJECT_ENTITIES_NAMESPACE.'\\This\\Is\\A\\TestEntity',
            ]
        );
        $createdFile = self::WORK_DIR.'/'.self::TEST_PROJECT_ENTITIES_NAMESPACE.'/This/Is/A/TestEntity.php';
        $this->assertFileExists($createdFile);
        $contents = file_get_contents($createdFile);
        $this->assertNotContains('Template', $contents);
    }
}
