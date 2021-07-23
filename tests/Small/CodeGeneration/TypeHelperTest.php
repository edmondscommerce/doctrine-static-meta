<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small\CodeGeneration;

use Doctrine\Common\Collections\Collection;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use PHPUnit\Framework\TestCase;

/**
 * Class TypeHelperTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Small\CodeGeneration
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\TypeHelper
 */
class TypeHelperTest extends TestCase
{
    /**
     * @var TypeHelper
     */
    private TypeHelper $helper;

    public function setup(): void
    {
        $this->helper = new TypeHelper();
    }

    /**
     * @test
     * @small
     *      */
    public function getType(): void
    {
        $expectedTypesToVars = [
            'string' => 'string',
            'float'  => 1.01,
            'int'    => 2,
            'bool'   => true,
            'null'   => null,
        ];
        foreach ($expectedTypesToVars as $expected => $var) {
            self::assertSame($expected, $this->helper->getType($var));
        }
    }

    /**
     * @test
     * @small
     *      */
    public function normaliseValueToType(): void
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
                [$value, $expected] = $valueAndExpected;
                $normalised = $this->helper->normaliseValueToType($value, $type);
                self::assertSame($expected, $normalised);
            }
        }
    }

    public function provideStripNull(): array
    {
        return [
            'null|string' => ['null|string', 'string'],
            'string|null' => ['string|null', 'string'],
            '?string'     => ['?string', 'string'],
        ];
    }

    /**
     * @test
     * @dataProvider provideStripNull
     */
    public function stripNullTest(string $type, string $expected): void
    {
        $actual = $this->helper->stripNull($type);
        self::assertSame($expected, $actual);
    }

    public function provideIsIterableType(): array
    {
        return [
            MappingHelper::TYPE_ARRAY  => [MappingHelper::TYPE_ARRAY, true],
            '\\' . Collection::class   => ['\\' . Collection::class, true],
            Collection::class          => [Collection::class, false],
            MappingHelper::TYPE_STRING => [MappingHelper::TYPE_STRING, false],
        ];
    }

    /**
     * @test
     * @dataProvider provideIsIterableType
     */
    public function isIterableTypeTest(string $type, bool $expected): void
    {
        $actual = $this->helper->isIterableType($type);
        self::assertSame($expected, $actual);
    }
}
