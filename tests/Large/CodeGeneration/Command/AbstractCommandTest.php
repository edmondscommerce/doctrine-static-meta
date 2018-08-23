<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends AbstractTest
{

    /**
     * @param AbstractCommand $command
     *
     * @return CommandTester
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getCommandTester(AbstractCommand $command): CommandTester
    {
        $application = new Application();
        //$_SERVER[ConfigInterface::PARAM_ENTITIES_PATH] = static::WORK_DIR.'/src/Entities';
        $helperSet = ConsoleRunner::createHelperSet(
            $this->container->get(EntityManagerInterface::class)
        );
        $application->setHelperSet($helperSet);
        $application->add($command);

        return new CommandTester($command);
    }

    protected function getEntityPath(string $entityFqn): string
    {
        $entityPath = str_replace(
            '\\',
            '/',
            \substr(
                $entityFqn,
                \strpos(
                    $entityFqn,
                    'Entities\\'
                ) + \strlen('Entities\\')
            )
        );

        return '/' . $entityPath;
    }

    /**
     * @return array
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    protected function generateEntities(): array
    {
        $entityGenerator = $this->container->get(EntityGenerator::class);
        $entityGenerator->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
                        ->setPathToProjectRoot(static::WORK_DIR);
        $baseNamespace = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                         . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\' . $this->getName();
        $entityFqns    = [
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