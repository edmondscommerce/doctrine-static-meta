<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Schema;

use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory
 * @medium
 */
class MysqliConnectionFactoryTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/MysqliConnectionFactoryTest/';

    /**
     * @test
     */
    public function testCanCreateConnectionFromEntityManager()
    {
        $this->createDatabase();
        $connection = $this->getFactory()->createFromEntityManager($this->getEntityManager());
        self::assertNotEmpty($connection->client_info);
    }

    private function getFactory(): MysqliConnectionFactory
    {
        return new MysqliConnectionFactory();
    }
}
