<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\AbstractFilesystemItem
 */
class FileNoPathTest extends TestCase
{

    /**
     * @test
     * @small
     *
     */
    public function canUpdatePath(): void
    {
        $object = new File();
        $path   = '/path/to/nowhere';
        $object->setPath($path);
        self::assertSame($path, $object->getPath());
    }

    /**
     * @test
     * @small
     */
    public function constructNoParams(): File
    {
        $object = new File();
        self::assertNull($object->getPath());
        self::assertNull($object->getContents());

        return $object;
    }

    /**
     * @test
     * @small
     * @depends constructNoParams
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetObjectWhenNoPathSet(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('$this->path is not set');
        $object->getSplFileInfo();
    }

    /**
     * @test
     * @small
     * @depends constructNoParams
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnCreateWhenNoPathSet(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('$this->path is not set');
        $object->create();
    }

    /**
     * @test
     * @small
     * @depends constructNoParams
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnLoadContentsWhenNoPathSet(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('$this->path is not set');
        $object->loadContents();
    }

    /**
     * @test
     * @small
     * @depends constructNoParams
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnPutContentsWhenNoPathSet(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('$this->path is not set');
        $object->putContents();
    }

    /**
     * @test
     * @small
     * @depends constructNoParams
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetFileInfoWhenNoPathSet(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('$this->path is not set');
        $object->getSplFileInfo();
    }

    /**
     * @test
     * @small
     * @depends constructNoParams
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetFileObjectWhenNoPathSet(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('$this->path is not set');
        $object->getSplFileObject();
    }
}
