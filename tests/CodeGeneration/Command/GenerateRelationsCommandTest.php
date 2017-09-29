<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateRelationsCommandTest extends AbstractCodeGenerationTest
{

    protected function generateEntities()
    {
        $entityGenerator = new EntityGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        $baseNamespace   = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
            .self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $entities        = [
            $baseNamespace.'\\FirstEntity',
            $baseNamespace.'\\Second\\SecondEntity',
            $baseNamespace.'\\Now\\Third\\ThirdEntity',
        ];
        foreach ($entities as $fullyQualifiedName) {
            $entityGenerator->generateEntity($fullyQualifiedName);
        }
    }

    public function testGenerateRelationsNoFiltering()
    {
        $this->generateEntities();
        $application = new Application();
        $helperSet   = require __DIR__.'/../../../cli-config.php';
        $application->setHelperSet($helperSet);
        $command = new GenerateRelationsCommand();
        $application->add($command);
        $tester = new CommandTester($command);
        $tester->execute(
            [
                '-'.GenerateEntityCommand::ARG_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-'.GenerateEntityCommand::ARG_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
            ]
        );
        $createdFile = self::WORK_DIR.'/'.self::TEST_PROJECT_ENTITIES_NAMESPACE.'/This/Is/A/TestEntity.php';
        $this->assertTemplateCorrect($createdFile);
    }
}
