<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Testing\Testing;

use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\AbstractEntityTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFiller;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFiller
 * @large
 */
class FakerDataFillerTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/FakerDataFillerTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                TestCodeGenerator::TEST_ENTITY_ALL_ARCHETYPE_FIELDS;

    public function setUp()
    {
        parent::setUp();
        $this->generateTestCode();
    }

    /**
     * @test
     */
    public function itCanFillADtoWithFakerData(): void
    {
        $dto = $this->getEntityDtoFactory()
                    ->createEmptyDtoFromEntityFqn(self::TEST_ENTITY);
        $this->getFillerForEntityFqn(self::TEST_ENTITY)->fillDtoFieldsWithData($dto);

        $getters  = $this->getDsmForTestEntity()->getGetters();
        $expected = [
            'getId'                        => 'Ramsey\Uuid\Uuid',
            'getString'                    => 'Sunt odio et eos saepe numquam inventore. Distinctio quia reiciendis ut quibusdam voluptatum aut et. Odio architecto cum eligendi dignissimos odit. Voluptas voluptatem est saepe itaque.',
            'getDatetime'                  => 'DateTime',
            'getFloat'                     => 1.326637024386653,
            'getDecimal'                   => 2494587.23,
            'getInteger'                   => 1629474955,
            'getText'                      => 'Aut quibusdam voluptates est in dolorum sed. Non repudiandae quia suscipit laborum aliquid vitae. Enim deleniti accusantium quae eum explicabo. Et ut architecto voluptatibus architecto corporis quis.',
            'isBoolean'                    => true,
            'getJson'                      => '{
    "string": "Fugit est illo maiores cupiditate ea magni voluptatem. Doloribus ipsa et qui dolorem ut at voluptas. Quibusdam incidunt magnam et id veritatis.",
    "float": 1.3,
    "nested": {
        "string": "Omnis quibusdam rerum voluptatem aut. Aut ut architecto ad placeat quaerat. Consequatur optio nihil in maxime aut molestiae illum.",
        "float": 11009230
    }
}',
            'getBusinessIdentifierCode'    => 'GOAEPITEOWT',
            'getCountryCode'               => 'SO',
            'getDateTimeSettableNoDefault' => 'DateTime',
            'getDateTimeSettableOnce'      => null,
            'isDefaultsDisabled'           => false,
            'isDefaultsEnabled'            => true,
            'isDefaultsNull'               => false,
            'getEmailAddress'              => 'hilbert17@hotmail.com',
            'getEnum'                      => 'bar',
            'getIpAddress'                 => '10.51.253.229',
            'getIsbn'                      => '9786306802784',
            'getLocaleIdentifier'          => 'ii_CN',
            'getNullableString'            => null,
        ];
        $actual   = [];
        foreach (array_keys($expected) as $getter) {
            if (!method_exists($dto, $getter)) {
                continue;
            }
            $got = $dto->$getter();
            if ($got instanceof \DateTime) {
                $got = \get_class($got);
            }
            if ($got instanceof UuidInterface) {
                $got = \get_class($got);
            }
            if (\is_object($got) && method_exists($got, '__toString')) {
                $got = (string)$got;
            }
            $actual[$getter] = $got;
        }
        self::assertSame($expected, $actual);
    }

    private function getFillerForEntityFqn(string $entityFqn): FakerDataFiller
    {
        $dsm = $this->getDsmForTestEntity();
        $dsm->setMetaData($this->getEntityManager()->getMetadataFactory()->getMetadataFor($entityFqn));

        return new FakerDataFiller(
            $dsm,
            AbstractEntityTest::FAKER_DATA_PROVIDERS,
            1000.0
        );
    }

    private function getDsmForTestEntity(): DoctrineStaticMeta
    {
        $entityFqn = self::TEST_ENTITY;

        return $entityFqn::getDoctrineStaticMeta();
    }
}