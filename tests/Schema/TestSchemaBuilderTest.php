<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Schema;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\EntityManager\DevEntityManagerFactory;

class TestSchemaBuilderTest extends AbstractTest
{
    const WORK_DIR = __DIR__ . '/../../var/' . __FILE__;

    /**
     * @var TestSchemaBuilder
     */
    protected $testschemaBuilder;

    public function setup()
    {
        $entityManager = DevEntityManagerFactory::setupAndGetEm();
        $this->testschemaBuilder = new TestSchemaBuilder($entityManager);
    }

    public function testGetTestSchemaName()
    {
        $actual = $this->testschemaBuilder->getTestSchemaName();
        $expected = '';
        $this->assertEquals($expected, $actual);
    }
}
