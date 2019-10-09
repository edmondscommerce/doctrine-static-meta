<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CreateConstraintCommand;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use ReflectionException;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\CreateConstraintCommand
 */
class CreateConstraintCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/CreateConstraintCommandTest/';

    private const CONSTRAINTS_PATH = self::WORK_DIR . '/src/Validation/Constraints';

    /**
     * @test
     * @large
     * @throws DoctrineStaticMetaException
     */
    public function createConstraint(): void
    {
        $command = $this->container->get(CreateConstraintCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . CreateConstraintCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . CreateConstraintCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT =>
                    self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . CreateConstraintCommand::OPT_CONSTRAINT_SHORT_NAME_SHORT  => 'IsYellowConstraint',
            ]
        );

        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsYellowConstraint.php');
        self::assertFileExists(self::CONSTRAINTS_PATH . '/IsYellowConstraintValidator.php');
    }
}
