<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Small;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @covers \EdmondsCommerce\DoctrineStaticMeta\MappingHelper
 * @small
 */
class MappingHelperTest extends TestCase
{
    /**
     * @test
     */
    public function getTableNameForEntityFqn(): void
    {
        $expected  = '`bar_baz`';
        $entityFqn = '\\DSM\\Test\\Project\\Entities\\Bar\\Baz';
        $actual    = MappingHelper::getTableNameForEntityFqn($entityFqn);
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function getColumnName(): void
    {
        $fieldNamesToExpectedColumnNames = [
            'test'                   => '`test`',
            'longThingWithCamelCase' => '`long_thing_with_camel_case`',
        ];
        foreach ($fieldNamesToExpectedColumnNames as $field => $expected) {
            $actual = MappingHelper::getColumnNameForField($field);
            self::assertSame($expected, $actual);
        }
    }

    /**
     * @test
     */
    public function itCanHandleTheWordStaffForPluralAndSingular(): void
    {
        $entityFqn = '\\Test\\Project\\Entity\\Staff';
        $plural    = MappingHelper::getPluralForFqn($entityFqn);
        $singular  = MappingHelper::getSingularForFqn($entityFqn);
        self::assertNotSame($plural, $singular);
    }

    public function providerEntityFqnToSingular(): array
    {
        $namespace = 'My\\Test\\Project\\Entities\\';

        return [
            'Product'     => [$namespace . 'Product', 'product'],
            'ProductData' => [$namespace . 'ProductData', 'productData'],
            'Data'        => [$namespace . 'Data', 'data'],
            'Person'      => [$namespace . 'Person', 'person'],
            'People'      => [$namespace . 'People', 'person'],
        ];
    }

    /**
     * @param string $entityFqn
     *
     * @test
     * @dataProvider providerEntityFqnToSingular
     */
    public function getSingularForFqn(string $entityFqn, string $singular)
    {
        $expected = $singular;
        $actual   = MappingHelper::getSingularForFqn($entityFqn);
        self::assertSame($expected, $actual);
    }
}
