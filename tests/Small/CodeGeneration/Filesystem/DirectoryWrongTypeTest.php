<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Directory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Directory
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\AbstractFilesystemItem
 * @small
 */
class DirectoryWrongTypeTest extends TestCase
{
    /**
     * @test
     */
    public function itExceptsIfThePathExistsButIsTheWrongType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('path is not the correct type');
        $directory = new Directory(__FILE__);
        $directory->exists();
    }
}
