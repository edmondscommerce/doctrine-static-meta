<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;

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
        parent::setup();
        $entityGenerator    = $this->getEntityGenerator();
        $relationsGenerator = $this->getRelationsGenerator();
        foreach (self::TEST_ENTITIES as $fqn) {
            $entityGenerator->generateEntity($fqn);
            $relationsGenerator->generateRelationCodeForEntity($fqn);
        }
        $relationsGenerator->setEntityHasRelationToEntity(
            self::TEST_ENTITIES[0],
            RelationsGenerator::HAS_MANY_TO_MANY,
            self::TEST_ENTITIES[1]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @throws Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testGetTableNameForEntityFqn()
    {
        $expected  = '`bar_baz`';
        $entityFqn = '\\DSM\\Test\\Project\\Entities\\Bar\\Baz';
        $actual    = MappingHelper::getTableNameForEntityFqn($entityFqn);
        $this->assertEquals($expected, $actual);
    }
}
