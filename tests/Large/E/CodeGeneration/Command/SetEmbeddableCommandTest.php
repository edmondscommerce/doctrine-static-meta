<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetEmbeddableCommand;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class SetEmbeddableCommandTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetEmbeddableCommand
 */
class SetEmbeddableCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/SetEmbeddableCommandTest/';


    /**
     * @test
     * @large
     * @throws DoctrineStaticMetaException
     */
    public function setEmbeddable(): void
    {
        $command  = $this->container->get(SetEmbeddableCommand::class);
        $tester   = $this->getCommandTester($command);
        $entities = $this->getTestEntityFqns();

        $tester->execute(
            [
                '-' . SetEmbeddableCommand::OPT_ENTITY_SHORT                 => $entities[0],
                '-' . SetEmbeddableCommand::OPT_EMBEDDABLE_SHORT             => HasMoneyEmbeddableTrait::class,
                '-' . SetEmbeddableCommand::OPT_PROJECT_ROOT_PATH_SHORT      => $this->copiedWorkDir,
                '-' . SetEmbeddableCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->copiedRootNamespace,
            ]
        );

        $tester->execute(
            [
                '-' . SetEmbeddableCommand::OPT_ENTITY_SHORT                 => $entities[1],
                '-' . SetEmbeddableCommand::OPT_EMBEDDABLE_SHORT             => HasFullNameEmbeddableTrait::class,
                '-' . SetEmbeddableCommand::OPT_PROJECT_ROOT_PATH_SHORT      => $this->copiedWorkDir,
                '-' . SetEmbeddableCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->copiedRootNamespace,
            ]
        );

        $tester->execute(
            [
                '--' . SetEmbeddableCommand::OPT_ENTITY                      => $entities[2],
                '--' . SetEmbeddableCommand::OPT_EMBEDDABLE                  => HasAddressEmbeddableTrait::class,
                '-' . SetEmbeddableCommand::OPT_PROJECT_ROOT_PATH_SHORT      => $this->copiedWorkDir,
                '-' . SetEmbeddableCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->copiedRootNamespace,
            ]
        );

        self::assertTrue($this->qaGeneratedCode());
    }
}
