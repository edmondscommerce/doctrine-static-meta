<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\FieldGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\SetFieldCommand
 * @large
 */
class SetFieldCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/SetFieldCommandTest/';

    private const TEST_ENTITY = self::COMMAND_TEST_ENTITIES[0];

    private const TEST_ENTITY_PATH = self::WORK_DIR . '/src/Entities/Person.php';

    /**
     * @test
     */
    public function setField(): void
    {
        $command = $this->container->get(SetFieldCommand::class);
        $tester  = $this->getCommandTester($command);
        foreach (FieldGenerator::STANDARD_FIELDS as $fieldFqn) {
            $tester->execute(
                [
                    '-' . SetFieldCommand::OPT_FIELD_SHORT                  => $fieldFqn,
                    '-' . SetFieldCommand::OPT_ENTITY_SHORT                 => self::TEST_ENTITY,
                    '-' . SetFieldCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                    '-' . SetFieldCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                ]
            );
            $this->assertFileContains(self::TEST_ENTITY_PATH, $fieldFqn);
        }
    }
}
