<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Attribute\WeightEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use ReflectionException;
use ts\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta
 * @medium
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DoctrineStaticMetaTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/DoctrineStaticMetaTest';

    private const TEST_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;

    private const TEST_CUSTOM_RELATION_FQN =
        self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_CUSTOM_RELATION;

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
    public function itCanGetAndSetMetaData(): void
    {
        $expected = new ClassMetadata(self::TEST_ENTITY_FQN);
        $actual   = $this->getDsm()->setMetaData($expected)->getMetaData();
        self::assertSame($expected, $actual);
    }

    private function getDsm($entityFqn = self::TEST_ENTITY_FQN): DoctrineStaticMeta
    {
        return new DoctrineStaticMeta($entityFqn);
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
            'weightEmbeddable'   => WeightEmbeddable::class,
        ];
        $actual   = $this->getDsm(self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES)
                         ->getEmbeddableProperties();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetGetters(): void
    {
        $expected = [
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
        $actual   = $this->getDsm()->getGetters();
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
    public function itCanGetReflection(): void
    {
        $expected = new ReflectionClass(self::TEST_ENTITY_FQN);
        $actual   = $this->getDsm()->getReflectionClass();
        self::assertEquals($expected, $actual);
    }

    /**
     * @throws ReflectionException
     * @test
     */
    public function itCanGetRequiredRelationProperties(): void
    {
        $expected  = [
            'person'         =>
                [
                    'My\Test\Project\Entity\Interfaces\PersonInterface',
                    false,
                ],
            'orderAddresses' =>
                [
                    'My\Test\Project\Entity\Interfaces\Order\AddressInterface',
                    true,
                ],
        ];
        $entityFqn = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ORDER;
        $required  = $this->getDsm($entityFqn)
                          ->getRequiredRelationProperties();
        $actual    = [];
        foreach ($required as $property => $relation) {
            $actual[$property] = [
                $relation->getRelationEntityFqn(),
                $relation->isPluralRelation(),
            ];
        }
        self::assertSame($expected, $actual);
    }

    /**
     * @throws ReflectionException
     * @test
     */
    public function itCanGetRequiredRelationPropertiesForCustomRelations(): void
    {
        $expected  = [
            'assignedTo' => [
                'My\Test\Project\Entity\Interfaces\CompanyInterface',
                false,

            ],
            'company'    => [
                'My\Test\Project\Entity\Interfaces\CompanyInterface',
                false,
            ],

        ];
        $entityFqn = self::TEST_CUSTOM_RELATION_FQN;
        $required  = $this->getDsm($entityFqn)
                          ->getRequiredRelationProperties();
        $actual    = [];
        foreach ($required as $property => $relation) {
            $actual[$property] = [
                $relation->getRelationEntityFqn(),
                $relation->isPluralRelation(),
            ];
        }
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanGetSetters(): void
    {
        $expected = [
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
        $actual   = $this->getDsm()->getSetters();
        self::assertSame($expected, $actual);
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
        $expectedCount = 34;
        $actual        = $this->getDsm()->getStaticMethods();
        self::assertCount($expectedCount, $actual);
    }

    /**
     * @param string $getterName
     * @param string $expectedPropertyName
     *
     * @test
     * @dataProvider provideGetterNamesToPropertyNames
     */
    public function itCanGetThePropertyNameFromTheGetterName(string $getterName, string $expectedPropertyName): void
    {
        $actualPropertyName = $this->getDsm()->getPropertyNameFromGetterName($getterName);
        self::assertSame($expectedPropertyName, $actualPropertyName);
    }

    /**
     * @param string $propertyName
     * @param string $expectedSetterName
     *
     * @test
     * @dataProvider provideSetterNamesToPropertyNames
     */
    public function itCanGetTheSetterNameForThePropertyName(string $expectedSetterName, string $propertyName): void
    {
        $actualSetterName = $this->getDsm()->getSetterNameFromPropertyName($propertyName);
        self::assertSame($expectedSetterName, $actualSetterName);
    }

    /**
     * @param string $setterName
     * @param string $expectedPropertyName
     *
     * @test
     * @dataProvider provideSetterNamesToPropertyNames
     */
    public function itCanSetThePropertyNameFromTheSetterName(string $setterName, string $expectedPropertyName): void
    {
        $actualPropertyName = $this->getDsm()->getPropertyNameFromSetterName($setterName);
        self::assertSame($expectedPropertyName, $actualPropertyName);
    }

    public function provideGetterNamesToPropertyNames(): array
    {
        return [
            'getAttributesEmails'  => ['getAttributesEmails', 'attributesEmails'],
            'getAttributesAddress' => ['getAttributesAddress', 'attributesAddress'],
            'getCompanyDirector'   => ['getCompanyDirector', 'companyDirector'],
            'getLargeRelation'     => ['getLargeRelation', 'largeRelation'],
            'getId'                => ['getId', 'id'],
            'getString'            => ['getString', 'string'],
            'getDatetime'          => ['getDatetime', 'datetime'],
            'getFloat'             => ['getFloat', 'float'],
            'getDecimal'           => ['getDecimal', 'decimal'],
            'getInteger'           => ['getInteger', 'integer'],
            'getText'              => ['getText', 'text'],
            'isBoolean'            => ['getBoolean', 'boolean'],
            'getArray'             => ['getArray', 'array'],
            'getObject'            => ['getObject', 'object'],
        ];
    }

    public function provideSetterNamesToPropertyNames(): array
    {
        return [
            'setAttributesEmails'  => ['setAttributesEmails', 'attributesEmails'],
            'setAttributesAddress' => ['setAttributesAddress', 'attributesAddress'],
            'setCompanyDirector'   => ['setCompanyDirector', 'companyDirector'],
            'setLargeRelation'     => ['setLargeRelation', 'largeRelation'],
            'setId'                => ['setId', 'id'],
            'setString'            => ['setString', 'string'],
            'setDatetime'          => ['setDatetime', 'datetime'],
            'setFloat'             => ['setFloat', 'float'],
            'setDecimal'           => ['setDecimal', 'decimal'],
            'setInteger'           => ['setInteger', 'integer'],
            'setText'              => ['setText', 'text'],
            'setBoolean'           => ['setBoolean', 'boolean'],
            'setArray'             => ['setArray', 'array'],
            'setObject'            => ['setObject', 'object'],
        ];
    }
}
