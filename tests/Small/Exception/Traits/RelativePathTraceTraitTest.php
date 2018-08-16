<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Small\Exception\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use PHPUnit\Framework\TestCase;

class RelativePathTraceTraitTest extends TestCase
{
    public function testGetTraceAsStringRelativePath(): void
    {
        try {
            throw new DoctrineStaticMetaException('oh noes');
        } catch (DoctrineStaticMetaException $e) {
            $expected = "\n\n#0 /vendor/phpunit/phpunit";
            $actual   = $e->getTraceAsStringRelativePath();
            self::assertSame(0, strpos($actual, $expected));
        }
    }
}
