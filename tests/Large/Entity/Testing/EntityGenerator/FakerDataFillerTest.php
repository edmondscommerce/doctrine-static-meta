<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Testing\EntityGenerator;

use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\AbstractEntityTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFiller;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

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
// phpcs:disable
        $expected = [
            'getId'                        => Uuid::class,
            'getString'                    => 'Dolorem dolorem non eveniet aut quo maiores et. Iusto quia maiores dolor. Voluptate sed in tempora harum occaecati quibusdam autem. Alias dolore quos quia ducimus.',
            'getDatetime'                  => 'DateTime',
            'getFloat'                     => 0.63014643983236363,
            'getDecimal'                   => 1550514.3700000001,
            'getInteger'                   => 105072201,
            'getText'                      => 'Consequatur sed eum ut. Architecto et voluptatibus sint. Consequatur recusandae deleniti accusamus asperiores quia ipsam.',
            'isBoolean'                    => false,
            'getJson'                      => '{
    "string": "Fugit est illo maiores cupiditate ea magni voluptatem. Doloribus ipsa et qui dolorem ut at voluptas. Quibusdam incidunt magnam et id veritatis.",
    "float": 1.3,
    "nested": {
        "string": "Omnis quibusdam rerum voluptatem aut. Aut ut architecto ad placeat quaerat. Consequatur optio nihil in maxime aut molestiae illum.",
        "float": 11009230
    }
}',
            'getBusinessIdentifierCode'    => 'ZLMLBROG34U',
            'getCountryCode'               => 'CA',
            'getDateTimeSettableNoDefault' => 'DateTime',
            'getDateTimeSettableOnce'      => null,
            'isDefaultsDisabled'           => false,
            'isDefaultsEnabled'            => false,
            'isDefaultsNull'               => false,
            'getEmailAddress'              => 'lparker@yahoo.com',
            'getEnum'                      => 'bar',
            'getIpAddress'                 => '10.179.197.177',
            'getIsbn'                      => '4461979482',
            'getLocaleIdentifier'          => 'ms_BN',
            'getNullableString'            => 'Alice. \'Off with her face like the right thing to eat her up in great fear lest she should meet.',
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
            $this->getNamespaceHelper(),
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
