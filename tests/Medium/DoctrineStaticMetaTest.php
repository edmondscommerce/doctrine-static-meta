<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use ts\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta
 * @medium
 */
class DoctrineStaticMetaTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/DoctrineStaticMetaTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;

    protected static $buildOnce = true;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
    }

    /**
     * @test
     */
    public function itCanGetGetters(): void
    {
        $expected = [
            1 => 'getId',
            2 => 'getUuid',
            3 => 'getString',
            4 => 'getDatetime',
            5 => 'getFloat',
            6 => 'getDecimal',
            7 => 'getInteger',
            8 => 'getText',
            9 => 'isBoolean',
            10 => 'getJson',
            11 => 'getAttributesAddress',
            12 => 'getAttributesEmails',
            13 => 'getCompanyDirector',
            14 => 'getLargeRelation',
        ];
        $actual   = $this->getDsm()->getGetters();
        self::assertSame($expected, $actual);
    }

    private function getDsm($entityFqn = self::TEST_ENTITY_FQN): DoctrineStaticMeta
    {
        return new DoctrineStaticMeta($entityFqn);
    }

    /**
     * @test
     */
    public function itCanGetSetters(): void
    {
        $expected = [
            'getId'                => 'setId',
            'getString'            => 'setString',
            'getDatetime'          => 'setDatetime',
            'getFloat'             => 'setFloat',
            'getDecimal'           => 'setDecimal',
            'getInteger'           => 'setInteger',
            'getText'              => 'setText',
            'isBoolean'            => 'setBoolean',
            'getJson'              => 'setJson',
            'getAttributesAddress' => 'setAttributesAddress',
            'getAttributesEmails'  => 'setAttributesEmails',
            'getCompanyDirector'   => 'setCompanyDirector',
            'getLargeRelation'     => 'setLargeRelation',
        ];
        $actual   = $this->getDsm()->getSetters();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetReflection(): void
    {
        $expected = new ReflectionClass(self::TEST_ENTITY_FQN);
        $actual   = $this->getDsm()->getReflectionClass();
        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetShortName(): void
    {
        $expected = 'Person';
        $actual   = $this->getDsm()->getShortName();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetPlural(): void
    {
        $expected = 'people';
        $actual   = $this->getDsm()->getPlural();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetSingular(): void
    {
        $expected = 'person';
        $actual   = $this->getDsm()->getSingular();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetStaticMethods(): void
    {
        $expectedCount = 32;
        $actual        = $this->getDsm()->getStaticMethods();
        self::assertCount($expectedCount, $actual);
    }

    /**
     * @test
     */
    public function itCanGetAndSetMetaData(): void
    {
        $expected = new ClassMetadata(self::TEST_ENTITY_FQN);
        $actual   = $this->getDsm()->setMetaData($expected)->getMetaData();
        self::assertSame($expected, $actual);
    }

    /**
     * @throws \ReflectionException
     * @test
     */
    public function itCanGetRequiredRelationProperties(): void
    {
        $expected  = [
            'person'         => [
                'My\Test\Project\Entity\Interfaces\PersonInterface',
            ],
            'orderAddresses' => [
                'My\Test\Project\Entity\Interfaces\Order\AddressInterface[]',
            ],
        ];
        $entityFqn = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER;
        $actual    = $this->getDsm($entityFqn)
                          ->getRequiredRelationProperties();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetEmbeddableProperties(): void
    {
        $expected = [
            'moneyEmbeddable'    => MoneyEmbeddable::class,
            'addressEmbeddable'  => AddressEmbeddable::class,
            'fullNameEmbeddable' => FullNameEmbeddable::class,
        ];
        $actual   = $this->getDsm(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES)
                         ->getEmbeddableProperties();
        self::assertSame($expected, $actual);
    }
}
