<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends AbstractTest
{

    protected function getCommandTester(AbstractCommand $command): CommandTester
    {
        $application                                 = new Application();
        $_SERVER[ConfigInterface::paramEntitiesPath] = static::WORK_DIR . '/src/Entities';
        $helperSet                                   = require __DIR__ . '/../../../cli-config.php';
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
            static::TEST_PROJECT_ROOT_NAMESPACE,
            static::WORK_DIR,
            static::TEST_PROJECT_ENTITIES_FOLDER
        );
        $baseNamespace   = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
            . static::TEST_PROJECT_ENTITIES_FOLDER.'\\'.$this->getName();
        $entityFqns      = [
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
