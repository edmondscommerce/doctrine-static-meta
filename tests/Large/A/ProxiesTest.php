<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\A;

use Doctrine\Common\Proxy\Proxy as DeprecatedProxy;
use Doctrine\ORM\Proxy\Proxy;
use Doctrine\ORM\Proxy\ProxyFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

use function array_map;

/**
 * @coversNothing
 */
class ProxiesTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/ProxiesTest/';
    protected static bool $buildOnce = true;
    /**
     * @var ProxyFactory
     */
    private ProxyFactory $proxyFactory;
    /**
     * It is a Proxy, the others are just to make PHPStan happy
     *
     * @var Proxy|DeprecatedProxy|EntityInterface
     */
    private EntityInterface|DeprecatedProxy|Proxy $proxy;
    private array                                 $testEntityFqns;

    public function setup():void
    {
        parent::setUp();
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

    private function getTestEntityFqns(): array
    {
        $copiedRootNamespace = $this->copiedRootNamespace;

        return array_map(
            static function (string $entityFqn) use ($copiedRootNamespace): string {
                return $copiedRootNamespace . $entityFqn;
            },
            TestCodeGenerator::TEST_ENTITIES
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

    /**
     * @test
     * @large
     */
    public function proxyObjectsCanGetGettersAndSetters(): void
    {
        $expectedSetters = [
            'getAttributesAddress' => 'setAttributesAddress',
            'getAttributesEmails'  => 'setAttributesEmails',
            'getCompanyDirector'   => 'setCompanyDirector',
            'getLargeRelation'     => 'setLargeRelation',
            'getId'                => 'setId',
            'getString'            => 'setString',
            'getDatetime'          => 'setDatetime',
            'getFloat'             => 'setFloat',
            'getDecimal'           => 'setDecimal',
            'getInteger'           => 'setInteger',
            'getText'              => 'setText',
            'isBoolean'            => 'setBoolean',
            'getArray'             => 'setArray',
            'getObject'            => 'setObject',
        ];
        $actualSetters   = $this->proxy::getDoctrineStaticMeta()->getSetters();
        self::assertSame($expectedSetters, $actualSetters);
        $expectedGetters = [
            'getAttributesAddress',
            'getAttributesEmails',
            'getCompanyDirector',
            'getLargeRelation',
            'getId',
            'getUuid',
            'getString',
            'getDatetime',
            'getFloat',
            'getDecimal',
            'getInteger',
            'getText',
            'isBoolean',
            'getArray',
            'getObject',
        ];
        $actualGetters   = $this->proxy::getDoctrineStaticMeta()->getGetters();
        self::assertSame($expectedGetters, $actualGetters);
    }
}
