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

    private const TEST_FILE_1     = '/src/Entity/Fields/Traits/BooleanFieldTrait.php';
    private const OVERRIDE_FILE_1 =
        '/build/overrides/src/Entity/Fields/Traits/BooleanFieldTrait.053d6c517a25452ff8e8e466ee21ce7a.php';
    private const TEST_FILE_2     = '/src/Entity/Fields/Interfaces/BooleanFieldInterface.php';
    private const OVERRIDE_FILE_2 =
        '/build/overrides/src/Entity/Fields/Interfaces/BooleanFieldInterface.beabbb194ddbbfbed0cfb4417ba00735.php';

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            mkdir(self::WORK_DIR . '/build/overrides', 0777, true);
            $this->createOverrides();
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
    }

    private function createOverrides()
    {
        /**
         * @var FileOverrider $overrider
         */
        $overrider = $this->container->get(FileOverrider::class);
        $overrider->setPathToProjectRoot(self::WORK_DIR);
        $overrider->createNewOverride(self::WORK_DIR . self::TEST_FILE_1);
        $overrider->createNewOverride(self::WORK_DIR . self::TEST_FILE_2);
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function updateProject(): void
    {
        \ts\file_put_contents($this->copiedWorkDir . self::OVERRIDE_FILE_1, 'this is updated in the overrides');
        $command = $this->container->get(OverridesUpdateCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . OverridesUpdateCommand::OPT_PROJECT_ROOT_PATH_SHORT => $this->copiedWorkDir,
                '-' .
                OverridesUpdateCommand::OPT_OVERRIDE_ACTION_SHORT         => OverridesUpdateCommand::ACTION_FROM_PROJECT,
            ]
        );
        self::assertFileEquals($this->copiedWorkDir . self::TEST_FILE_1, $this->copiedWorkDir . self::OVERRIDE_FILE_1);
        self::assertFileEquals($this->copiedWorkDir . self::TEST_FILE_2, $this->copiedWorkDir . self::OVERRIDE_FILE_2);
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
                OverridesUpdateCommand::OPT_OVERRIDE_ACTION_SHORT         => OverridesUpdateCommand::ACTION_FROM_PROJECT,
            ]
        );
        self::assertFileEquals($this->copiedWorkDir . self::OVERRIDE_FILE_1, $this->copiedWorkDir . self::TEST_FILE_1);
        self::assertFileEquals($this->copiedWorkDir . self::OVERRIDE_FILE_2, $this->copiedWorkDir . self::TEST_FILE_2);
    }
}
