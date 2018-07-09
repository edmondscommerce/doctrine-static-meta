<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class MappingHelperTest extends TestCase
{
    public function testGetTableNameForEntityFqn(): void
    {
        $expected  = '`bar_baz`';
        $entityFqn = '\\DSM\\Test\\Project\\Entities\\Bar\\Baz';
        $actual    = MappingHelper::getTableNameForEntityFqn($entityFqn);
        self::assertSame($expected, $actual);
    }

    public function testGetColumnName(): void
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
     * @small
     */
    public function itCanHandleTheWordStaffForPluralAndSingular(): void
    {
        $entityFqn = '\\Test\\Project\\Entity\\Staff';
        $plural    = MappingHelper::getPluralForFqn($entityFqn);
        $singular  = MappingHelper::getSingularForFqn($entityFqn);
        self::assertNotSame($plural, $singular);
    }
}
