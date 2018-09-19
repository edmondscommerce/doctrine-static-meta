<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Directory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Directory
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\AbstractFilesystemItem
 */
class DirectoryNoPathTest extends TestCase
{

    /**
     * @test
     * @small
     *
     */
    public function canUpdatePath(): void
    {
        $object = new Directory();
        $path   = '/path/to/nowhere';
        $object->setPath($path);
        self::assertSame($path, $object->getPath());
    }

    /**
     * @test
     * @small
     */
    public function constructNoParams(): Directory
    {
        $object = new Directory();
        self::assertNull($object->getPath());

        return $object;
    }

    /**
     * @test
     * @small
     * @depends constructNoParams
     *
     * @param Directory $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetObjectWhenNoPathSet(Directory $object): void
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
     * @param Directory $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnCreateWhenNoPathSet(Directory $object): void
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
     * @param Directory $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetFileInfoWhenNoPathSet(Directory $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('$this->path is not set');
        $object->getSplFileInfo();
    }
}