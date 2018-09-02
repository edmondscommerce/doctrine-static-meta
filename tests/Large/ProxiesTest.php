<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium;

use Doctrine\ORM\Proxy\Proxy;
use Doctrine\ORM\Proxy\ProxyFactory;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @coversNothing
 */
class ProxiesTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/ProxiesTest/';

    protected static $buildOnce = true;

    /**
     * @var ProxyFactory
     */
    private $proxyFactory;

    /**
     * @var Proxy
     */
    private $proxy;

    private $testEntityFqns;

    public function setup()
    {
        parent::setup();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $proxyDir = $this->copiedWorkDir . '/proxies';
        mkdir($proxyDir, 0777, true);
        $this->proxyFactory   = new ProxyFactory(
            $this->getEntityManager(),
            $proxyDir,
            $this->copiedRootNamespace . '\\Proxies'
        );
        $this->testEntityFqns = $this->getTestEntityFqns();
        $this->proxyFactory->generateProxyClasses($this->getClassMetaDatas());
        $testEntity = current($this->testEntityFqns);
        $this->getEntitySaver()->save($this->createEntity($testEntity));
        $this->proxy = $this->proxyFactory->getProxy($testEntity, ['id' => 1]);
    }

    private function getClassMetaDatas(): array
    {
        $return        = [];
        $entityManager = $this->getEntityManager();
        foreach ($this->testEntityFqns as $entityFqn) {
            $return[] = $entityManager->getClassMetadata($entityFqn);
        }

        return $return;
    }

    private function getTestEntityFqns(): array
    {
        $copiedRootNamespace = $this->copiedRootNamespace;

        return \array_map(
            function ($in) use ($copiedRootNamespace) {
                return str_replace(TestCodeGenerator::TEST_PROJECT_ROOT_NAMESPACE, $copiedRootNamespace, $in);
            },
            TestCodeGenerator::TEST_ENTITIES
        );
    }

    /**
     * @test
     * @medium
     */
    public function proxyObjectsCanGetGettersAndSetters()
    {
        self::assertNotEmpty($this->proxy->getSetters());
        self::assertNotEmpty($this->proxy->getGetters());
    }
}
