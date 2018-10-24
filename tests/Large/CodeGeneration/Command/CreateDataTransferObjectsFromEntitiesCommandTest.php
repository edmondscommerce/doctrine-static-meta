<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\FinaliseBuildCommand;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\FinaliseBuildCommand
 * @large
 */
class CreateDataTransferObjectsFromEntitiesCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = self::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/CreateDataTransferObjectsFromEntitiesCommandTest/';
    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
    }

    /**
     * @test
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function createDtos(): void
    {
        $command = $this->container->get(FinaliseBuildCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . FinaliseBuildCommand::OPT_PROJECT_ROOT_PATH_SHORT      => self::WORK_DIR,
                '-' . FinaliseBuildCommand::OPT_PROJECT_ROOT_NAMESPACE_SHORT =>
                    self::TEST_PROJECT_ROOT_NAMESPACE,
            ]
        );

        self::assertFileExists(self::WORK_DIR . '/src/Entity/DataTransferObjects/PersonDto.php');
    }
}
