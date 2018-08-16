<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Small;

use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\MappingHelper
 */
class MappingHelperTest extends TestCase
{
    /**
     * @test
     * @small
     * @covers ::getTableNameForEntityFqn
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
     * @small
     * @covers ::getColumnNameForField
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
     * @small
     * @covers ::getPluralForFqn ::getSingularForFqn
     */
    public function itCanHandleTheWordStaffForPluralAndSingular(): void
    {
        $entityFqn = '\\Test\\Project\\Entity\\Staff';
        $plural    = MappingHelper::getPluralForFqn($entityFqn);
        $singular  = MappingHelper::getSingularForFqn($entityFqn);
        self::assertNotSame($plural, $singular);
    }
}
