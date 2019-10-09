<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableSkeletonCommand;

/**
 * @large
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEmbeddableSkeletonCommand
 */
class GenerateEmbeddableSkeletonCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = self::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/GenerateEmbeddableSkeletonCommandTest/';

    /**
     * @test
     */
    public function itCanBeRun(): void
    {
        $command = $this->container->get(GenerateEmbeddableSkeletonCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEmbeddableSkeletonCommand::OPT_PROJECT_ROOT_PATH_SHORT            => self::WORK_DIR,
                '-' . GenerateEmbeddableSkeletonCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT       =>
                    self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . GenerateEmbeddableSkeletonCommand::OPT_NEW_EMBEDDABLE_CATEGORY_NAME_SHORT => 'Stuff',
                '-' . GenerateEmbeddableSkeletonCommand::OPT_NEW_EMBEDDABLE_NAME_SHORT          => 'Thing',
            ]
        );

        self::assertTrue($this->qaGeneratedCode());
    }
}
