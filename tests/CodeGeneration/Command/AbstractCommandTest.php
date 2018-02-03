<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends AbstractTest
{

    protected function getCommandTester(AbstractCommand $command): CommandTester
    {
        $application                                   = new Application();
        //$_SERVER[ConfigInterface::PARAM_ENTITIES_PATH] = static::WORK_DIR.'/src/Entities';
        $helperSet                                     = ConsoleRunner::createHelperSet(
            $this->container->get(EntityManager::class)
        );
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

        return '/'.$entityPath;
    }

    /**
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function generateEntities(): array
    {
        $entityGenerator = $this->container->get(EntityGenerator::class);
        $entityGenerator->setProjectRootNamespace(static::TEST_PROJECT_ROOT_NAMESPACE)
                        ->setPathToProjectSrcRoot(static::WORK_DIR)
                        ->setEntitiesFolderName(static::TEST_PROJECT_ENTITIES_FOLDER);
        $baseNamespace = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                         .static::TEST_PROJECT_ENTITIES_FOLDER.'\\'.$this->getName();
        $entityFqns    = [
            $baseNamespace.'\\FirstEntity',
            $baseNamespace.'\\Second\\SecondEntity',
            $baseNamespace.'\\Now\\Third\\ThirdEntity',
        ];
        foreach ($entityFqns as $fullyQualifiedName) {
            $entityGenerator->generateEntity($fullyQualifiedName);
        }

        return $entityFqns;
    }
}
