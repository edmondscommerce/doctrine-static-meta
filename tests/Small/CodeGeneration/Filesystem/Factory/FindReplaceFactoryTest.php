<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration\Filesystem\Factory;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File\FindReplace;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory\FindReplaceFactory
 */
class FindReplaceFactoryTest extends TestCase
{
    /**
     * @test
     * @small
     */
    public function itCanCreateFindReplaceObjects(): void
    {
        $file    = new File();
        $created = $this->getFactory()->create($file);
        self::assertInstanceOf(FindReplace::class, $created);
    }

    private function getFactory(): FindReplaceFactory
    {
        return new FindReplaceFactory();
    }
}
