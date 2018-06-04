<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class UsesPHPMetaDataTraitUnitTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Traits
 * @SupressWarnings(PHPMD.UnusedLocalVariable)
 */
class UsesPHPMetaDataTraitUnitTest extends TestCase
{


    public function testGetGettersAndSetters()
    {
        $testClass = new class ()
        {
            use UsesPHPMetaDataTrait;

            protected static function setCustomRepositoryClass(ClassMetadataBuilder $builder)
            {
                // TODO: Implement setCustomRepositoryClass() method.
            }

            public function getThis()
            {
            }

            public function getThat()
            {
            }

            public function setThis()
            {
            }

            public function setThat()
            {
            }

            private function getSomething()
            {
            }

            private function setSomething()
            {
            }

            protected function getSomethingElse()
            {
            }

            protected function setSomethingElse()
            {
            }
        };
        $expected  = [
            'getThis',
            'getThat',
        ];
        $actual    = $testClass->getGetters();
        $this->assertSame($expected, $actual);
        $expected = [
            'setThis',
            'setThat',
        ];
        $actual   = $testClass->getSetters();
        $this->assertSame($expected, $actual);
    }
}
