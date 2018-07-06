<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class UsesPHPMetaDataTraitUnitTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Traits
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class UsesPHPMetaDataTraitUnitTest extends TestCase
{


    public function testGetGettersAndSetters(): void
    {
        $testClass = new class ()
        {
            use UsesPHPMetaDataTrait;

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
