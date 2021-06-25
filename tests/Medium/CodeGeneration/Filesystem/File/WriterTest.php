<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Filesystem\File;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\Writer
 */
class WriterTest extends TestCase
{
    private const WORK_DIR = AbstractTest::VAR_PATH . '/' . AbstractTest::TEST_TYPE_MEDIUM . '/WriterTest';

    public static function setupBeforeClass():void
    {
        mkdir(self::WORK_DIR, 0777, true);
    }

    /**
     * @test
     * @medium
     * @throws DoctrineStaticMetaException
     */
    public function itCanWriteFiles(): void
    {
        $file = $this->getFile();
        $this->getWriter()->write($file);
        self::assertFileExists($file->getPath());
        self::assertSame($file->getContents(), \ts\file_get_contents($file->getPath()));
    }

    private function getFile(): File
    {
        $file = new File(self::WORK_DIR . '/subdir/file.txt');
        $file->setContents('some contents');

        return $file;
    }

    private function getWriter(): Writer
    {
        return new Writer();
    }
}
