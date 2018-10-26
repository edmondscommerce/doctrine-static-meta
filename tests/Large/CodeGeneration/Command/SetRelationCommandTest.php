<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand
 * @large
 */
class SetRelationCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/SetRelationCommandTest/';


    /**
     * @test
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function setRelation(): void
    {
        $owningEntityFqn =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON);
        $ownedEntityFqn  =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE .
                                TestCodeGenerator::TEST_ENTITY_ALL_ARCHETYPE_FIELDS);

        $command = $this->container->get(SetRelationCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => $this->copiedWorkDir,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->copiedRootNamespace,
                '-' . SetRelationCommand::OPT_ENTITY1_SHORT                   => $owningEntityFqn,
                '-' . SetRelationCommand::OPT_HAS_TYPE_SHORT                  => RelationsGenerator::HAS_MANY_TO_MANY,
                '-' . SetRelationCommand::OPT_ENTITY2_SHORT                   => $ownedEntityFqn,
            ]
        );
        $entityPath       = $this->getNamespaceHelper()->getEntityFileSubPath($owningEntityFqn);
        $owningEntityPath = $this->copiedWorkDir . '/src/Entities/' . $entityPath;
        self::assertContains(
            'HasAllStandardLibraryFieldsTestEntitiesOwningManyToMany',
            \file_get_contents($owningEntityPath)
        );
    }

    /**
     * @test
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function setRelationWithoutRelationPrefix(): void
    {
        $owningEntityFqn =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON);
        $ownedEntityFqn  =
            $this->getCopiedFqn(self::TEST_ENTITIES_ROOT_NAMESPACE .
                                TestCodeGenerator::TEST_ENTITY_NAME_SPACING_ANOTHER_CLIENT);

        $command = $this->container->get(SetRelationCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => $this->copiedWorkDir,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->copiedRootNamespace,
                '-' . SetRelationCommand::OPT_ENTITY1_SHORT                   => $owningEntityFqn,
                '-' . SetRelationCommand::OPT_HAS_TYPE_SHORT                  => 'ManyToMany',
                '-' . SetRelationCommand::OPT_ENTITY2_SHORT                   => $ownedEntityFqn,
            ]
        );
        $namespaceHelper  = new NamespaceHelper();
        $entityPath       = $namespaceHelper->getEntityFileSubPath($owningEntityFqn);
        $owningEntityPath = $this->entitiesPath . $entityPath;
        self::assertContains(
            'HasAnotherDeeplyNestedClientsOwningManyToMany',
            \file_get_contents($owningEntityPath)
        );
    }
}
