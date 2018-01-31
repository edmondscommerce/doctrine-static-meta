<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateRelationsCommandTest extends AbstractCommandTest
{
    const WORK_DIR = VAR_PATH . '/GenerateEntityCommandTest/';

    public function testGenerateRelationsNoFiltering()
    {
        $entityFqns = $this->generateEntities();
        $command    = new GenerateRelationsCommand();
        $tester     = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
            ]
        );
        $createdFiles    = [];
        $namespaceHelper = new NamespaceHelper();
        foreach ($entityFqns as $entityFqn) {
            $entityName   = (new \ReflectionClass($entityFqn))->getShortName();
            $entityPlural = ucfirst($entityFqn::getPlural());
            $entityPath   = $namespaceHelper->getEntitySubPath($entityFqn, self::TEST_PROJECT_ROOT_NAMESPACE . '\\' . self::TEST_PROJECT_ENTITIES_NAMESPACE, false);
            $createdFiles = array_merge(
                $createdFiles,
                glob($this->entitiesPath . '/Relations/' . $entityPath . '/Traits/Has' . $entityName . '/*.php'),
                glob($this->entitiesPath . '/Relations/' . $entityPath . '/Traits/Has' . $entityPlural . '/*.php'),
                glob($this->entitiesPath . '/Relations/' . $entityPath . '/Traits/*.php')
            );
        }
        foreach ($createdFiles as $createdFile) {
            $this->assertTemplateCorrect($createdFile);
        }
    }
}
