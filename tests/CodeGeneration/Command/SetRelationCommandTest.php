<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\AbstractCodeGenerationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SetRelationCommandTest extends AbstractCommandTest
{
    const WORK_DIR = VAR_PATH.'/SetRelationCommandTest/';

    public function testSetRelation()
    {
        list($owningEntityFqn, $ownedEntityFqn,) = $this->generateEntities();

        $command = $this->container->get(SetRelationCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-'.SetRelationCommand::OPT_ENTITY1_SHORT                   => $owningEntityFqn,
                '-'.SetRelationCommand::OPT_HAS_TYPE_SHORT                  => RelationsGenerator::HAS_MANY_TO_MANY,
                '-'.SetRelationCommand::OPT_ENTITY2_SHORT                   => $ownedEntityFqn,
            ]
        );
        $namespaceHelper  = $this->container->get(NamespaceHelper::class);
        $entityPath       = $namespaceHelper->getEntityFileSubPath(
            $owningEntityFqn,
            self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.self::TEST_PROJECT_ENTITIES_FOLDER
        );
        $owningEntityPath = $this->entitiesPath.$entityPath;
        $this->assertContains('HasSecondEntitiesOwningManyToMany', file_get_contents($owningEntityPath));
    }

    public function testSetRelationWithoutRelationPrefix()
    {
        list(, $owningEntityFqn, $ownedEntityFqn) = $this->generateEntities();

        $command = $this->container->get(SetRelationCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-'.GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-'.SetRelationCommand::OPT_ENTITY1_SHORT                   => $owningEntityFqn,
                '-'.SetRelationCommand::OPT_HAS_TYPE_SHORT                  => 'ManyToMany',
                '-'.SetRelationCommand::OPT_ENTITY2_SHORT                   => $ownedEntityFqn,
            ]
        );
        $namespaceHelper  = new NamespaceHelper();
        $entityPath       = $namespaceHelper->getEntityFileSubPath($owningEntityFqn,
                                                                   self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.self::TEST_PROJECT_ENTITIES_FOLDER);
        $owningEntityPath = $this->entitiesPath.$entityPath;
        $this->assertContains('HasThirdEntitiesOwningManyToMany', file_get_contents($owningEntityPath));
    }
}
