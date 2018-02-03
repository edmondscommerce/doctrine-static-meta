<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta;

class ContainerTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH.'/ContainerTest';

    public function testLoadServices()
    {
        foreach (Container::SERVICES as $id) {
            $service = $this->container->get($id);
            $this->assertInstanceOf($id, $service);
        }
    }
}
