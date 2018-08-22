<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class SetRelationCommandTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Command
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetRelationCommand
 */
class SetRelationCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE . '/SetRelationCommandTest/';

    /**
     * @test
     * @large
     * @covers ::execute
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setRelation(): void
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
            'Has' . \ucfirst($this->getName()) . 'SecondSecondEntitiesOwningManyToMany',
            \file_get_contents($owningEntityPath)
        );
    }

    /**
     * @test
     * @large
     * @covers ::execute
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setRelationWithoutRelationPrefix(): void
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
            'Has' . \ucfirst($this->getName()) . 'NowThirdThirdEntitiesOwningManyToMany',
            \file_get_contents($owningEntityPath)
        );
    }
}
