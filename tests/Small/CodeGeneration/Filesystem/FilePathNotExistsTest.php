<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\AbstractFilesystemItem
 */
class FilePathNotExistsTest extends TestCase
{

    /**
     * @test
     * @small
     */
    public function constructWithPathParamThatDoesNotExist(): File
    {
        $pathNotExists = '/path/not/exists';
        $object        = new File($pathNotExists);
        self::assertSame($pathNotExists, $object->getPath());
        self::assertFalse($object->exists());
        self::assertNull($object->getContents());

        return $object;
    }

    /**
     * @test
     * @small
     * @depends constructWithPathParamThatDoesNotExist
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetObjectWhenPathDoesNotExist(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('file does not exist at path');
        $object->getSplFileInfo();
    }

    /**
     * @test
     * @small
     * @depends constructWithPathParamThatDoesNotExist
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnCreateWhenPathDoesNotExist(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('directory does not exist at path');
        $object->create();
    }

    /**
     * @test
     * @small
     * @depends constructWithPathParamThatDoesNotExist
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnLoadContentsWhenPathDoesNotExist(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('file does not exist at path');
        $object->loadContents();
    }

    /**
     * @test
     * @small
     * @depends constructWithPathParamThatDoesNotExist
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnPutContentsWhenPathDoesNotExist(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('file does not exist at path');
        $object->putContents();
    }

    /**
     * @test
     * @small
     * @depends constructWithPathParamThatDoesNotExist
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetFileInfoWhenPathDoesNotExist(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('file does not exist at path');
        $object->getSplFileInfo();
    }

    /**
     * @test
     * @small
     * @depends constructWithPathParamThatDoesNotExist
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetFileObjectWhenPathDoesNotExist(File $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('file does not exist at path');
        $object->getSplFileObject();
    }
}