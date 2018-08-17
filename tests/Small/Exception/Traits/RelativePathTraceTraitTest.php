<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Exception\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use PHPUnit\Framework\TestCase;

/**
 * Class RelativePathTraceTraitTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Small\Exception\Traits
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
 */
class RelativePathTraceTraitTest extends TestCase
{
    /**
     * @test
     * @small
     * @covers ::getTraceAsStringRelativePath
     */
    public function getTraceAsStringRelativePath(): void
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
