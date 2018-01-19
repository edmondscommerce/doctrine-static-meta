<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends AbstractCodeGenerationTest
{

    protected function getCommandTester(AbstractCommand $command): CommandTester
    {
        $application = new Application();
        $helperSet = require __DIR__ . '/../../../cli-config.php';
        $application->setHelperSet($helperSet);
        $application->add($command);
        $tester = new CommandTester($command);
        return $tester;
    }

    protected function getEntityPath(string $entityFqn)
    {
        $entityPath = str_replace(
            '\\',
            '/',
            substr(
                $entityFqn,
                strpos(
                    $entityFqn,
                    'Entities\\'
                ) + strlen('Entities\\')
            )
        );
        return '/' . $entityPath;
    }

    protected function generateEntities(): array
    {
        $entityGenerator = new EntityGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        $baseNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
            . self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $entityFqns = [
            $baseNamespace . '\\FirstEntity',
            $baseNamespace . '\\Second\\SecondEntity',
            $baseNamespace . '\\Now\\Third\\ThirdEntity',
        ];
        foreach ($entityFqns as $fullyQualifiedName) {
            $entityGenerator->generateEntity($fullyQualifiedName);
        }

        return $entityFqns;
    }
}
