<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FileCreationTransaction;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateRelationsCommandTest extends AbstractCommandTest
{
    const WORK_DIR = VAR_PATH.'/GenerateEntityCommandTest/';

    /**
     * @depends ContainerTest::testLoadServices
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function testGenerateRelationsNoFiltering()
    {
        $entityFqns      = $this->generateEntities();
        $namespaceHelper = $this->container->get(NamespaceHelper::class);
        $command         = $this->container->get(GenerateRelationsCommand::class);
        $tester          = $this->getCommandTester($command);
        $tester->execute(
            [
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
            ]
        );
        $createdFiles = [];

        foreach ($entityFqns as $entityFqn) {
            $entityName   = (new \ReflectionClass($entityFqn))->getShortName();
            $entityPlural = ucfirst($entityFqn::getPlural());
            $entityPath   = $namespaceHelper->getEntitySubPath(
                $entityFqn,
                self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.self::TEST_PROJECT_ENTITIES_FOLDER
            );
            $createdFiles = array_merge(
                $createdFiles,
                glob($this->entitiesPath.'/Relations/'.$entityPath.'/Traits/Has'.$entityName.'/*.php'),
                glob($this->entitiesPath.'/Relations/'.$entityPath.'/Traits/Has'.$entityPlural.'/*.php'),
                glob($this->entitiesPath.'/Relations/'.$entityPath.'/Traits/*.php')
            );
        }
        $this->assertNotEmpty($createdFiles);
        foreach ($createdFiles as $createdFile) {
            $this->assertTemplateCorrect($createdFile);
        }
    }
}
