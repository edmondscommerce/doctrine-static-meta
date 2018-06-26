<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use PHPUnit\Framework\TestCase;

class NamespaceHelperTest extends TestCase
{
    /**
     * @var NamespaceHelper
     */
    private static $helper;

    public static function setupBeforeClass()
    {
        self::$helper = new NamespaceHelper();
    }

    public function testCropSuffix()
    {
        $fqn      = 'FooBar';
        $suffix   = 'Bar';
        $expected = 'Foo';
        $actual   = self::$helper->cropSuffix($fqn, $suffix);
        $this->assertSame($expected, $actual);
    }

    public function testSwapSuffix()
    {
        $fqn           = 'FooBar';
        $currentSuffix = 'Bar';
        $newSuffix     = 'Baz';
        $expected      = 'FooBaz';
        $actual        = self::$helper->swapSuffix($fqn, $currentSuffix, $newSuffix);
        $this->assertSame($expected, $actual);
    }

    public function testCropSuffixWhereSuffixNotInThere()
    {
        $fqn      = 'FooBar';
        $suffix   = 'Cheese';
        $expected = 'FooBar';
        $actual   = self::$helper->cropSuffix($fqn, $suffix);
        $this->assertSame($expected, $actual);
    }

    public function testGetObjectShortName()
    {

        $expectedToObjects = [
            'NamespaceHelperTest' => $this,
            'NamespaceHelper'     => self::$helper,
        ];
        $actual            = [];
        foreach ($expectedToObjects as $object) {
            $actual[self::$helper->getObjectShortName($object)] = $object;
        }
        $this->assertSame($expectedToObjects, $actual);
    }

    public function testGetObjectFqn()
    {

        $expectedToObjects = [
            \get_class($this)         => $this,
            \get_class(self::$helper) => self::$helper,
        ];
        $actual            = [];
        foreach ($expectedToObjects as $object) {
            $actual[self::$helper->getObjectFqn($object)] = $object;
        }
        $this->assertSame($expectedToObjects, $actual);
    }

    public function testGetClassShortName()
    {
        $expectedToFqns = [
            'NamespaceHelperTest' => \get_class($this),
            'Cheese'              => '\\Super\\Cheese',
        ];
        $actual         = [];
        foreach ($expectedToFqns as $fqn) {
            $actual[self::$helper->getClassShortName($fqn)] = $fqn;
        }
        $this->assertSame($expectedToFqns, $actual);
    }
}
