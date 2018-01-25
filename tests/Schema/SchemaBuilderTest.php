<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use Doctrine\ORM\EntityManager;
use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\EntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\RelationsGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\ConfigInterface;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;
use EdmondsCommerce\DoctrineStaticMeta\SimpleEnv;

/**
 * THIS JUST DOESN'T WORK. ISSUES AROUND CREATING AND THEN GETTING META DATA IN SAME PROCESS
 *
 * Class SchemaBuilderTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Schema
 */
class SchemaBuilderTest /* extends AbstractTest */
{
    const WORK_DIR = __DIR__ . '/../../var/SchemaBuilderTest';

    const TEST_ENTITY_POST = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Post';

    const TEST_ENTITY_COMMENT = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Comment';

    /**
     * @var SchemaBuilder
     */
    protected $schemaBuilder;

    /**
     * @var EntityManager
     */
    protected $schemaBuilderEntityManager;

    public function setup()
    {
        parent::setup();
        $entityGenerator = new EntityGenerator(self::TEST_PROJECT_ROOT_NAMESPACE, self::WORK_DIR);
        $entityGenerator->generateEntity(self::TEST_ENTITY_POST);
        $entityGenerator->generateEntity(self::TEST_ENTITY_COMMENT);

        $relationsGenerator = new RelationsGenerator(self::TEST_PROJECT_ROOT_NAMESPACE, self::WORK_DIR);
        $relationsGenerator->setEntityHasRelationToEntity(self::TEST_ENTITY_POST, RelationsGenerator::HAS_ONE_TO_MANY, self::TEST_ENTITY_COMMENT);

        $this->schemaBuilderEntityManager = $this->getTestEntityManager();
        $this->schemaBuilder              = new SchemaBuilder($this->schemaBuilderEntityManager);
        $this->schemaBuilder->createTables();
    }

    public function testGetDbName()
    {
        $actual   = $this->schemaBuilder->getDbName();
        $expected = 'doctrine_static_example_schemabuilder_test';
        $this->assertEquals($expected, $actual);
    }

    public function testResetDb()
    {
        $entityClassName = self::TEST_ENTITY_POST;
        $createdEntity   = new $entityClassName();
        $this->schemaBuilderEntityManager->persist($createdEntity);
        $this->schemaBuilderEntityManager->flush($createdEntity);

        $loadedEntity = $this->schemaBuilderEntityManager->find(self::TEST_ENTITY_POST, 1);
        $this->assertInstanceOf($loadedEntity, self::TEST_ENTITY_POST);

        $this->schemaBuilder->resetDb();
        $reLoadedEntity = $this->schemaBuilderEntityManager->find(self::TEST_ENTITY_POST, 1);
        $this->assertEmpty($reLoadedEntity);
    }
}
