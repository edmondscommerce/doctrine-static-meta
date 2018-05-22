<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

class ContainerTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/ContainerTest';

    public function testLoadServices()
    {
        foreach (Container::SERVICES as $id) {
            $service = $this->container->get($id);
            $this->assertInstanceOf($id, $service);
        }
    }
}
