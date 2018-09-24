<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Directory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Directory
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\AbstractFilesystemItem
 */
class DirectoryPathNotExistsTest extends TestCase
{

    /**
     * @test
     * @small
     */
    public function constructWithPathParamThatDoesNotExist(): Directory
    {
        $pathNotExists = '/path/not/exists';
        $object        = new Directory($pathNotExists);
        self::assertSame($pathNotExists, $object->getPath());
        self::assertFalse($object->exists());

        return $object;
    }

    /**
     * @test
     * @small
     * @depends constructWithPathParamThatDoesNotExist
     *
     * @param Directory $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetObjectWhenPathDoesNotExist(Directory $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('directory does not exist at path');
        $object->getSplFileInfo();
    }


    /**
     * @test
     * @small
     * @depends constructWithPathParamThatDoesNotExist
     *
     * @param Directory $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function assertExceptionOnGetFileInfoWhenPathDoesNotExist(Directory $object): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('directory does not exist at path');
        $object->getSplFileInfo();
    }
}
