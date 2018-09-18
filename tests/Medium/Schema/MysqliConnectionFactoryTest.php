<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Schema;

use EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Schema\MysqliConnectionFactory
 */
class MysqliConnectionFactoryTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/MysqliConnectionFactoryTest/';

    /**
     * @test
     */
    public function testCanCreateConnectionFromEntityManager()
    {
        $connection = $this->getFactory()->createFromEntityManager($this->getEntityManager());
        self::assertNotEmpty($connection->client_info);
    }

    private function getFactory(): MysqliConnectionFactory
    {
        return new MysqliConnectionFactory();
    }
}