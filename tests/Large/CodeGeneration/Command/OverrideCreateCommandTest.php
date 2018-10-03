<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Command;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverrideCreateCommand;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\OverrideCreateCommand
 */
class OverrideCreateCommandTest extends AbstractCommandTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/OverrideCreateCommandTest/';

    private const TEST_FILE     = '/src/Entities/Company.php';
    private const OVERRIDE_FILE = '/build/overrides/src/Entities/Company.d9a89847e0e84f9b468666ec6a0dd15b.php';

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            mkdir(self::WORK_DIR . '/build/overrides', 0777, true);
            self::$built = true;
        }
    }

    /**
     * @test
     * @large
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function createOverride(): void
    {
        $command = $this->container->get(OverrideCreateCommand::class);
        $tester  = $this->getCommandTester($command);
        $tester->execute(
            [
                '-' . OverrideCreateCommand::OPT_PROJECT_ROOT_PATH_SHORT => self::WORK_DIR,
                '-' . OverrideCreateCommand::OPT_OVERRIDE_FILE_SHORT     => self::WORK_DIR . self::TEST_FILE,
            ]
        );
        //phpcs: disable
        $overrideFile = self::OVERRIDE_FILE;
        $expectedOutput = <<<OUTPUT
Creating override for Company.php
Override created at: $overrideFile
OUTPUT;
        //phpcs: enable
        self::assertSame(trim($expectedOutput), trim($tester->getDisplay()));
        self::assertFileEquals(self::WORK_DIR . self::TEST_FILE, self::WORK_DIR . self::OVERRIDE_FILE);
    }
}
