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

    public function testGetObjectShortName()
    {

        $expectedToObjects = [
            'NamespaceHelperTest' => $this,
            'NamespaceHelper'=>self::$helper,
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
            \get_class($this) => $this,
            \get_class(self::$helper)=>self::$helper,
        ];
        $actual            = [];
        foreach ($expectedToObjects as $object) {
            $actual[self::$helper->getObjectFqn($object)] = $object;
        }
        $this->assertSame($expectedToObjects, $actual);
    }
}
