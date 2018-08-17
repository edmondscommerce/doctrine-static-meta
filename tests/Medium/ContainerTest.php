<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium;

use EdmondsCommerce\DoctrineStaticMeta\Container;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * Class ContainerTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Medium
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Container
 */
class ContainerTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE . '/ContainerTest';

    /**
     * @test
     * @medium
     * @covers ::get
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function loadServices(): void
    {
        foreach (Container::SERVICES as $id) {
            $service = $this->container->get($id);
            self::assertInstanceOf($id, $service);
        }
    }
}
