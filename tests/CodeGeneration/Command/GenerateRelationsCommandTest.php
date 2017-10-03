<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateRelationsCommandTest extends AbstractCodeGenerationTest
{

    protected function generateEntities(): array
    {
        $entityGenerator = new EntityGenerator(
            self::TEST_PROJECT_ROOT_NAMESPACE,
            self::WORK_DIR,
            self::TEST_PROJECT_ENTITIES_NAMESPACE
        );
        $baseNamespace   = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
            .self::TEST_PROJECT_ENTITIES_NAMESPACE;
        $entityFqns      = [
            $baseNamespace.'\\FirstEntity',
            $baseNamespace.'\\Second\\SecondEntity',
            $baseNamespace.'\\Now\\Third\\ThirdEntity',
        ];
        foreach ($entityFqns as $fullyQualifiedName) {
            $entityGenerator->generateEntity($fullyQualifiedName);
        }

        return $entityFqns;
    }

    public function testGenerateRelationsNoFiltering()
    {
        $entityFqns  = $this->generateEntities();
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
        $createdFiles = [];
        foreach ($entityFqns as $entityFqn) {
            $entityName   = (new \ReflectionClass($entityFqn))->getShortName();
            $entityPlural = ucfirst($entityFqn::getPlural());
            $entityPath   = str_replace(
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
            $createdFiles = array_merge(
                $createdFiles,
                glob($this->entitiesPath.'/Traits/Relations/'.$entityPath.'/Has'.$entityName.'/*.php'),
                glob($this->entitiesPath.'/Traits/Relations/'.$entityPath.'/Has'.$entityPlural.'/*.php'),
                glob($this->entitiesPath.'/Traits/Relations/'.$entityPath.'/*.php')
            );
        }
        foreach ($createdFiles as $createdFile) {
            $this->assertTemplateCorrect($createdFile);
        }
    }
}
