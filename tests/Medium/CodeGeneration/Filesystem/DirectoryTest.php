<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Directory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Directory
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\AbstractFilesystemItem
 */
class DirectoryTest extends TestCase
{
    private const WORK_DIR = AbstractTest::VAR_PATH . '/DirectoryTest';

    public static function setUpBeforeClass()
    {
        mkdir(self::WORK_DIR);
    }

    /**
     * @test
     * @medium
     */
    public function itCanCreateANewDirectory(): Directory
    {
        $path   = self::WORK_DIR . '/directoryToCreate';
        $object = new Directory($path);
        $object->create();
        self::assertDirectoryExists(self::WORK_DIR . '/directoryToCreate');
        self::assertSame($path, $object->getPath());
        self::assertSame(realpath($path), $object->getSplFileInfo()->getRealPath());

        return $object;
    }

    /**
     * @test
     * @medium
     * @depends itCanCreateANewDirectory
     *
     * @param Directory $object
     *
     * @throws DoctrineStaticMetaException
     */
    public function itCachesDirectoryObjectInstances(Directory $object): void
    {
        self::assertSame($object->getSplFileInfo(), $object->getSplFileInfo());
    }

    /**
     * @test
     * @medium
     */
    public function itCanLoadAnExistingDirectory(): void
    {
        $path = self::WORK_DIR . '/alreadyExists';
        mkdir($path);
        $object = new Directory($path);
        self::assertSame($path, $object->getPath());
        self::assertTrue($object->exists());
    }

    /**
     * @test
     * @medium
     */
    public function itCannotCreateADirectoryThatAlreadyExists(): void
    {
        $path = self::WORK_DIR . '/alreadyExists2';
        mkdir($path);
        $object = new Directory($path);
        $this->expectException(DoctrineStaticMetaException::class);
        $this->expectExceptionMessage('directory already exists at path ');
        $object->create();
    }

    /**
     * @test
     * @medium
     */
    public function itCanSetDirectoryPermissionsToUseWhenCreating()
    {
        $path        = self::WORK_DIR . '/hasCustomPermissions';
        $permissions = 0666;
        $object      = new Directory($path);
        $object->setCreateMode($permissions);
        $object->create();
        self::assertSame(decoct($permissions), decoct(fileperms($path) & 0777));
    }
}
