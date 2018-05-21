<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class MappingHelperTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/MappingHelperTest';

    public const TEST_ENTITY_FQN_BASE = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME;

    public const TEST_ENTITIES = [
        self::TEST_ENTITY_FQN_BASE.'\\Blah\\Foo',
        self::TEST_ENTITY_FQN_BASE.'\\Bar\\Baz',
    ];

    public const TEST_ENTITY_POST_CREATED        = self::TEST_ENTITY_FQN_BASE.'\\Meh';
    public const TEST_ENTITY_POST_CREATED_NESTED = self::TEST_ENTITY_FQN_BASE.'\\Nested\\Something\\Ho\\Hum';

    public function setup()
    {
        // simply overriding setup to disable parent::setup
    }


    public function testGetTableNameForEntityFqn()
    {
        $expected  = '`bar_baz`';
        $entityFqn = '\\DSM\\Test\\Project\\Entities\\Bar\\Baz';
        $actual    = MappingHelper::getTableNameForEntityFqn($entityFqn);
        $this->assertSame($expected, $actual);
    }

    public function testGetColumnName()
    {
        $fieldNamesToExpectedColumnNames = [
            'test'                   => '`test`',
            'longThingWithCamelCase' => '`long_thing_with_camel_case`',
        ];
        foreach ($fieldNamesToExpectedColumnNames as $field => $expected) {
            $actual = MappingHelper::getColumnNameForField($field);
            $this->assertSame($expected, $actual);
        }
    }
}
