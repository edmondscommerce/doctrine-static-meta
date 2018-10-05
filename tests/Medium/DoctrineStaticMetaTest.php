<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
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
    public function itCanGetGetters()
    {
        $expected = [
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
            'getLargeRelation',
        ];
        $actual   = $this->getDsm()->getGetters();
        self::assertSame($expected, $actual);
    }

    private function getDsm(): DoctrineStaticMeta
    {
        return new DoctrineStaticMeta(self::TEST_ENTITY_FQN);
    }

    /**
     * @test
     */
    public function itCanGetSetters()
    {
        $expected = [
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
            'getOrders'            => 'setOrders',
            'getLargeRelation'     => 'setLargeRelation',
        ];
        $actual   = $this->getDsm()->getSetters();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetReflection()
    {
        $expected = new ReflectionClass(self::TEST_ENTITY_FQN);
        $actual   = $this->getDsm()->getReflectionClass();
        self::assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetShortName()
    {
        $expected = 'Person';
        $actual   = $this->getDsm()->getShortName();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetPlural()
    {
        $expected = 'people';
        $actual   = $this->getDsm()->getPlural();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetSingular()
    {
        $expected = 'person';
        $actual   = $this->getDsm()->getSingular();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetStaticMethods()
    {
        $expectedCount = 32;
        $actual        = $this->getDsm()->getStaticMethods();
        self::assertCount($expectedCount, $actual);
    }

    /**
     * @test
     */
    public function itCanGetAndSetMetaData()
    {
        $expected = new ClassMetadata(self::TEST_ENTITY_FQN);
        $actual   = $this->getDsm()->setMetaData($expected)->getMetaData();
        self::assertSame($expected, $actual);
    }
}
