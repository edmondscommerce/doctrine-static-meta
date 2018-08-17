<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;

class SetRelationCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE . '/SetRelationCommandTest/';

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testSetRelation(): void
    {
        list($owningEntityFqn, $ownedEntityFqn,) = $this->generateEntities();

        $command = $this->container->get(SetRelationCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . SetRelationCommand::OPT_ENTITY1_SHORT                   => $owningEntityFqn,
                '-' . SetRelationCommand::OPT_HAS_TYPE_SHORT                  => RelationsGenerator::HAS_MANY_TO_MANY,
                '-' . SetRelationCommand::OPT_ENTITY2_SHORT                   => $ownedEntityFqn,
            ]
        );
        $namespaceHelper  = $this->container->get(NamespaceHelper::class);
        $entityPath       = $namespaceHelper->getEntityFileSubPath($owningEntityFqn);
        $owningEntityPath = $this->entitiesPath . $entityPath;
        self::assertContains(
            'HasTestSetRelationSecondSecondEntitiesOwningManyToMany',
            file_get_contents($owningEntityPath)
        );
    }

    /**
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testSetRelationWithoutRelationPrefix(): void
    {
        list(, $owningEntityFqn, $ownedEntityFqn) = $this->generateEntities();

        $command = $this->container->get(SetRelationCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . SetRelationCommand::OPT_ENTITY1_SHORT                   => $owningEntityFqn,
                '-' . SetRelationCommand::OPT_HAS_TYPE_SHORT                  => 'ManyToMany',
                '-' . SetRelationCommand::OPT_ENTITY2_SHORT                   => $ownedEntityFqn,
            ]
        );
        $namespaceHelper  = new NamespaceHelper();
        $entityPath       = $namespaceHelper->getEntityFileSubPath($owningEntityFqn);
        $owningEntityPath = $this->entitiesPath . $entityPath;
        self::assertContains(
            'HasTestSetRelationWithoutRelationPrefixNowThirdThirdEntitiesOwningManyToMany',
            file_get_contents($owningEntityPath)
        );
    }
}
