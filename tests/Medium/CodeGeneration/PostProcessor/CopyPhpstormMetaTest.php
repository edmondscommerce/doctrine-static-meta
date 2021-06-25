<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\PostProcessor;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\CopyPhpstormMeta;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\CopyPhpstormMeta
 */
class CopyPhpstormMetaTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/CopyPhpstormMetaTest';

    /**
     * @var CopyPhpstormMeta
     */
    private CopyPhpstormMeta $process;

    public function setup():void
    {
        parent::setUp();
        $this->process = new CopyPhpstormMeta();
        $this->process->setPathToProjectRoot(self::WORK_DIR);
    }

    /**
     * @test
     */
    public function itCanCopyTheDirectoryIfItDoesNotExistIdempotently(): void
    {
        $this->process->run();
        self::assertFileExists(self::WORK_DIR . '/.phpstorm.meta.php/build.php');
        self::assertFileExists(self::WORK_DIR . '/.phpstorm.meta.php/container.meta.php');
        self::assertFileExists(self::WORK_DIR . '/.phpstorm.meta.php/dtofactory.meta.php');
        self::assertFileExists(self::WORK_DIR . '/.phpstorm.meta.php/entityfactory.meta.php');
        $this->process->run();
        self::assertFileExists(self::WORK_DIR . '/.phpstorm.meta.php/build.php');
        self::assertFileExists(self::WORK_DIR . '/.phpstorm.meta.php/container.meta.php');
        self::assertFileExists(self::WORK_DIR . '/.phpstorm.meta.php/dtofactory.meta.php');
        self::assertFileExists(self::WORK_DIR . '/.phpstorm.meta.php/entityfactory.meta.php');
    }
}
