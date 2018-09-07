<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class GenerateEntityCommandTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\GenerateEntityCommand
 */
class GenerateEntityCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/GenerateEntityCommandTest/';

    /**
     * @test
     * @large
     * @covers ::execute
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws DoctrineStaticMetaException
     */
    public function generateEntity(): void
    {
        $command = $this->container->get(GenerateEntityCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . GenerateEntityCommand::OPT_FQN_SHORT                    => self::TEST_PROJECT_ROOT_NAMESPACE .
                                                                                 '\\'
                                                                                 .
                                                                                 AbstractGenerator::ENTITIES_FOLDER_NAME
                                                                                 .
                                                                                 '\\This\\Is\\A\\TestEntity',
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

    /**
     * @test
     * @large
     * @covers ::execute
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws DoctrineStaticMetaException
     */
    public function generateEntityWithoutUuid(): void
    {
        $command = $this->container->get(GenerateEntityCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . GenerateEntityCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT => self::TEST_PROJECT_ROOT_NAMESPACE,
                '-' . GenerateEntityCommand::OPT_FQN_SHORT                    => self::TEST_PROJECT_ROOT_NAMESPACE
                                                                                 .
                                                                                 '\\'
                                                                                 .
                                                                                 AbstractGenerator::ENTITIES_FOLDER_NAME
                                                                                 .
                                                                                 '\\This\\Is\\Another\\TestEntity',
                '-' . GenerateEntityCommand::OPT_UUID_SHORT                   => false,
            ]
        );

        $entityPath = $this->entitiesPath . '/This/Is/Another/TestEntity.php';

        $this->assertNoMissedReplacements($entityPath);
        $this->assertFileContains($entityPath, 'IntegerIdFieldTrait');
    }
}
