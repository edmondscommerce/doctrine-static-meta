<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SetRelationCommandTest extends AbstractCommandTest
{
    public function testGenerateRelationsNoFiltering()
    {
        $entityFqns = $this->generateEntities();

        $command = new SetRelationCommand();
        $tester = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . SetRelationCommand::OPT_ENTITY1_SHORT => $entityFqns[0],
                '-' . SetRelationCommand::OPT_RELATION_TYPE_SHORT => RelationsGenerator::HAS_MANY_TO_MANY,
                '-' . SetRelationCommand::OPT_ENTITY2_SHORT => $entityFqns[1]
            ]
        );
        $createdFiles = [];
        foreach ($entityFqns as $entityFqn) {
            $entityName = (new \ReflectionClass($entityFqn))->getShortName();
            $entityPlural = ucfirst($entityFqn::getPlural());
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
