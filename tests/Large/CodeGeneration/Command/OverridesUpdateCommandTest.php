<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverridesUpdateCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\FileOverrider;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverrideCreateCommand
 */
class OverridesUpdateCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/OverridesUpdateCommandTest/';

    private const TEST_FILE_1 = '/src/Entity/Fields/Traits/BooleanFieldTrait.php';
    private const TEST_FILE_2 = '/src/Entity/Fields/Interfaces/BooleanFieldInterface.php';
    protected static $buildOnce = true;
    private $overrideFile1;
    private $overrideFile2;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            mkdir(self::WORK_DIR . '/build/overrides', 0777, true);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->createOverrides();
    }

    private function createOverrides(): void
    {
        /**
         * @var FileOverrider $overrider
         */
        $overrider = $this->container->get(FileOverrider::class);
        $overrider->setPathToProjectRoot($this->copiedWorkDir);
        $this->overrideFile1 =
            realpath(
                $this->copiedWorkDir . $overrider->createNewOverride(
                    $this->copiedWorkDir . self::TEST_FILE_1
                )
            );
        $this->overrideFile2 = realpath(
            $this->copiedWorkDir . $overrider->createNewOverride(
                $this->copiedWorkDir . self::TEST_FILE_2
            )
        );
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function updateProject(): void
    {
        \ts\file_put_contents($this->overrideFile1, 'this is updated in the overrides');
        $command = $this->container->get(OverridesUpdateCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . OverridesUpdateCommand::OPT_PROJECT_ROOT_PATH_SHORT => $this->copiedWorkDir,
                '-' .
                OverridesUpdateCommand::OPT_OVERRIDE_ACTION_SHORT         => OverridesUpdateCommand::ACTION_TO_PROJECT,
            ]
        );
        $expectedOutput = <<<OUTPUT
Updating overrides toProject
+---------------------------------------------------------+
| /src/Entity/Fields/Interfaces/BooleanFieldInterface.php |
| /src/Entity/Fields/Traits/BooleanFieldTrait.php         |
+---------------------------------------------------------+
Overrides have been applied to project
OUTPUT;
        self::assertSame(trim($expectedOutput), trim($tester->getDisplay()));
        self::assertFileEquals($this->copiedWorkDir . self::TEST_FILE_1, $this->overrideFile1);
        self::assertFileEquals($this->copiedWorkDir . self::TEST_FILE_2, $this->overrideFile2);
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function updateOverrides(): void
    {
        \ts\file_put_contents($this->copiedWorkDir . self::TEST_FILE_1, 'this is updated in the project');
        $command = $this->container->get(OverridesUpdateCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . OverridesUpdateCommand::OPT_PROJECT_ROOT_PATH_SHORT => $this->copiedWorkDir,
                '-' .
                OverridesUpdateCommand::OPT_OVERRIDE_ACTION_SHORT         =>
                    OverridesUpdateCommand::ACTION_FROM_PROJECT,
            ]
        );
        $expectedOutput = <<<OUTPUT
Updating overrides fromProject
+---------------------------------------------------------+
| /src/Entity/Fields/Interfaces/BooleanFieldInterface.php |
| /src/Entity/Fields/Traits/BooleanFieldTrait.php         |
+---------------------------------------------------------+
Overrides have been updated from the project
OUTPUT;
        self::assertSame(trim($expectedOutput), trim($tester->getDisplay()));
        self::assertFileEquals($this->overrideFile1, $this->copiedWorkDir . self::TEST_FILE_1);
        self::assertFileEquals($this->overrideFile2, $this->copiedWorkDir . self::TEST_FILE_2);
    }
}
