<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\Entity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class UsesPHPMetaDataTraitUnitTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Traits
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class UsesPHPMetaDataTraitUnitTest extends TestCase
{

    /**
     * @test
     * @small
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait::getGetters
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\UsesPHPMetaDataTrait::getSetters
     */
    public function getGettersAndSetters(): void
    {
        $testClass = new class ()
        {
            use UsesPHPMetaDataTrait;

            public function __construct()
            {
                $this->runInitMethods();
            }

            protected static function setCustomRepositoryClass(ClassMetadataBuilder $builder): void
            {
                // TODO: Implement setCustomRepositoryClass() method.
            }

            public function getThis(): void
            {
            }

            public function getThat(): void
            {
            }

            public function setThis(): void
            {
            }

            public function setThat(): void
            {
            }

            private function getSomething(): void
            {
            }

            private function setSomething(): void
            {
            }

            protected function getSomethingElse(): void
            {
            }

            protected function setSomethingElse(): void
            {
            }
        };
        $expected  = [
            'getThis',
            'getThat',
        ];
        $actual    = $testClass->getGetters();
        self::assertSame($expected, $actual);
        $expected = [
            'setThis',
            'setThat',
        ];
        $actual   = $testClass->getSetters();
        self::assertSame($expected, $actual);
    }
}
