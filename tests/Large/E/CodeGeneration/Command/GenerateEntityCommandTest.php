<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\E\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use ReflectionException;

/**
 * Class GenerateEntityCommandTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand
 */
class GenerateEntityCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/GenerateEntityCommandTest/';

    /**
     * @test
     * @large
     * @throws DoctrineStaticMetaException
     * @throws ReflectionException
     */
    public function generateEntity(): void
    {
        $command   = $this->container->get(GenerateEntityCommand::class);
        $tester    = $this->getCommandTester($command);
        $entityFqn = $this->getCopiedFqn(
            self::TEST_ENTITIES_ROOT_NAMESPACE . '\\This\\Is\\A\\TestEntity'
        );
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => $this->copiedWorkDir,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => $this->copiedRootNamespace,
                '-' . GenerateEntityCommand::OPT_FQN_SHORT                    => $entityFqn,
            ]
        );
        $createdFiles = [
            $this->entitiesPath . '/This/Is/A/TestEntity.php',
            $this->entitiesPath . '/../../tests/Entities/This/Is/A/TestEntityTest.php',
        ];
        foreach ($createdFiles as $createdFile) {
            $this->assertNoMissedReplacements($createdFile);
        }
    }
}
