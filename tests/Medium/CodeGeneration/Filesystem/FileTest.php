<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\AbstractFilesystemItem
 */
class FileTest extends TestCase
{
    private const WORK_DIR = AbstractTest::VAR_PATH . '/' . AbstractTest::TEST_TYPE_MEDIUM . '/FileTest';

    public static function setUpBeforeClass()
    {
        mkdir(self::WORK_DIR);
    }

    /**
     * @test
     * @medium
     */
    public function itCanCreateANewFile(): File
    {
        $path   = self::WORK_DIR . '/fileToCreate.txt';
        $object = new File($path);
        $object->create();
        self::assertFileExists(self::WORK_DIR . '/fileToCreate.txt');
        self::assertSame($path, $object->getPath());
        self::assertNull($object->getContents());
        self::assertSame(realpath($path), $object->getSplFileObject()->getRealPath());
        self::assertSame(realpath($path), $object->getSplFileInfo()->getRealPath());

        return $object;
    }

    /**
     * @test
     * @medium
     * @depends itCanCreateANewFile
     *
     * @param File $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function itCachesFileObjectInstances(File $object): void
    {
        self::assertSame($object->getSplFileObject(), $object->getSplFileObject());
        self::assertSame($object->getSplFileInfo(), $object->getSplFileInfo());
    }

    /**
     * @test
     * @medium
     */
    public function itCanSetFileContents(): void
    {
        $path     = self::WORK_DIR . '/fileToSetContents.txt';
        $contents = 'set contents';
        $object   = new File($path);
        $object->setContents($contents)
               ->create()
               ->putContents();
        self::assertSame($contents, $object->getContents());
    }

    /**
     * @test
     * @medium
     */
    public function itWillFailIfTheFileDirectoryDoesNotExist(): void
    {
        $path   = self::WORK_DIR . '/dir/not/exists/file.txt';
        $object = new File($path);
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('directory does not exist at path');
        $object->create();
    }

    /**
     * @test
     * @medium
     */
    public function itCanCreateTheDirectoryBeforeCreatingTheFile(): void
    {
        $path   = self::WORK_DIR . '/dir/to/create/file.txt';
        $object = new File($path);
        $object->getDirectory()->create();
        $object->create();
        self::assertFileExists($path);
    }

    /**
     * @test
     * @medium
     */
    public function itCanLoadAnExistingFile(): void
    {
        $path     = self::WORK_DIR . '/alreadyExists.txt';
        $contents = 'already exists';
        \ts\file_put_contents($path, $contents);
        $object = new File($path);
        $object->loadContents();
        self::assertSame($contents, $object->getContents());
    }

    /**
     * @test
     * @medium
     */
    public function itCannotCreateAFileThatAlreadyExists(): void
    {
        $path     = self::WORK_DIR . '/alreadyExists.txt';
        $contents = 'already exists';
        \ts\file_put_contents($path, $contents);
        $object = new File($path);
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('file already exists at path ');
        $object->create();
    }

    /**
     * @test
     * @medium
     */
    public function itCanSetFilePermissionsToUseWhenCreating()
    {
        $path        = self::WORK_DIR . '/hasCustomPermissions.txt';
        $permissions = 0664;
        $object      = new File($path);
        $object->setCreateMode($permissions);
        $object->create();
        self::assertSame($permissions, fileperms($path) & 0777);
    }
}
