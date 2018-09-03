<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large;

use Doctrine\Common\Proxy\Proxy as DeprecatedProxy;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\ORM\Proxy\ProxyFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
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
     * It is a Proxy, the others are just to make PHPStan happy
     *
     * @var Proxy|DeprecatedProxy|EntityInterface
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
        $this->setupProxyFactory();
        $this->testEntityFqns = $this->getTestEntityFqns();
        $this->proxyFactory->generateProxyClasses($this->getClassMetaDatas());
        $testEntity = current($this->testEntityFqns);
        $this->getEntitySaver()->save($this->createEntity($testEntity));
        $this->proxy = $this->proxyFactory->getProxy($testEntity, ['id' => 1]);
    }

    private function setupProxyFactory(): void
    {
        $proxyDir = $this->copiedWorkDir . '/proxies';
        mkdir($proxyDir, 0777, true);
        $this->proxyFactory = new ProxyFactory(
            $this->getEntityManager(),
            $proxyDir,
            $this->copiedRootNamespace . '\\Proxies'
        );
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
            function (string $entityFqn) use ($copiedRootNamespace): string {
                return str_replace(TestCodeGenerator::TEST_PROJECT_ROOT_NAMESPACE, $copiedRootNamespace, $entityFqn);
            },
            TestCodeGenerator::TEST_ENTITIES
        );
    }

    /**
     * @test
     * @large
     */
    public function proxyObjectsCanGetGettersAndSetters()
    {
        $expectedSetters = [
            'setString',
            'setDatetime',
            'setFloat',
            'setDecimal',
            'setInteger',
            'setText',
            'setBoolean',
            'setJson',
            'setAttributesAddress',
            'setAttributesEmails',
            'addAttributesEmail',
            'setCompanyDirector',
            'setOrders',
            'addOrder',
        ];
        $actualSetters   = $this->proxy::getDoctrineStaticMeta()->getSetters();
        self::assertSame($expectedSetters, $actualSetters);
        $expectedGetters = [
            'getId',
            'getString',
            'getDatetime',
            'getFloat',
            'getDecimal',
            'getInteger',
            'getText',
            'isBoolean',
            'getJson',
            'getAttributesAddress',
            'getAttributesEmails',
            'getCompanyDirector',
            'getOrders',
        ];
        $actualGetters   = $this->proxy::getDoctrineStaticMeta()->getGetters();
        self::assertSame($expectedGetters, $actualGetters);
    }
}
