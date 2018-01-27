<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
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
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->getName() . '\\' . self::TEST_PROJECT_ROOT_NAMESPACE,
            ]
        );
        $createdFiles = [];
        foreach ($entityFqns as $entityFqn) {
            $entityName   = (new \ReflectionClass($entityFqn))->getShortName();
            $entityPlural = ucfirst($entityFqn::getPlural());
            $entityPath   = $this->getEntityPath($entityFqn);
            $createdFiles = array_merge(
                $createdFiles,
                glob($this->entitiesPath . '/Traits/Relations/' . $entityPath . '/Has' . $entityName . '/*.php'),
                glob($this->entitiesPath . '/Traits/Relations/' . $entityPath . '/Has' . $entityPlural . '/*.php'),
                glob($this->entitiesPath . '/Traits/Relations/' . $entityPath . '/*.php')
            );
        }
        foreach ($createdFiles as $createdFile) {
            $this->assertTemplateCorrect($createdFile);
        }
    }
}
