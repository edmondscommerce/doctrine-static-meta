<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use PHPUnit\Framework\TestCase;

class TypeHelperTest extends TestCase
{
    /**
     * @var TypeHelper
     */
    private $helper;

    public function setup()
    {
        $this->helper = new TypeHelper();
    }

    public function testGetTypeWorksAsExpected()
    {
        $expectedTypesToVars = [
            'string' => 'string',
            'float'  => 1.01,
            'int'    => 2,
            'bool'   => true,
            'null'   => null,
        ];
        foreach ($expectedTypesToVars as $expected => $var) {
            $this->assertSame($expected, $this->helper->getType($var));
        }
    }

    public function testTypeNormaliserWorksAsExpected()
    {
        $defaultValuesToTypes = [
            MappingHelper::PHP_TYPE_INTEGER => [
                [1, 1],
                ['1', 1],
                [' 1', 1],
                [' 1 ', 1],
            ],
            MappingHelper::PHP_TYPE_FLOAT   => [
                [1, 1.0],
                [1.0, 1.0],
                ['1', 1.0],
                ['1.1', 1.1],
                [' 1.1 ', 1.1],
                [' 1.1 ', 1.1],
            ],
            MappingHelper::PHP_TYPE_BOOLEAN => [
                ['true', true],
                ['false', false],
                ['TRUE', true],
                ['FALSE', false],
                [' TRue ', true],
                [' FaLse ', false],
            ],
        ];

        foreach ($defaultValuesToTypes as $type => $valueAndExpecteds) {
            foreach ($valueAndExpecteds as $valueAndExpected) {
                list($value, $expected) = $valueAndExpected;
                $normalised = $this->helper->normaliseValueToType($value, $type);
                $this->assertSame($expected, $normalised);
            }
        }
    }

}
