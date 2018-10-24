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
// phpcs:disable
        $expected = [
            'getId'                        => 'Ramsey\\Uuid\\Uuid',
            'getString'                    => 'Amet magni ut molestias architecto. Quia ea harum deleniti qui iure. Quis in est dolores maxime.',
            'getDatetime'                  => 'DateTime',
            'getFloat'                     => 0.12269837874354915,
            'getDecimal'                   => 7711877.5499999998,
            'getInteger'                   => 1431204125,
            'getText'                      => 'Voluptatum aut maxime itaque voluptas quam perspiciatis. Et et eos dolorem iure quos. Quas qui necessitatibus voluptatem perferendis.',
            'isBoolean'                    => true,
            'getJson'                      => '{
    "string": "Temporibus quo tempore iste aspernatur. Eos eum nobis id vero. Perspiciatis vel et dolor suscipit. Hic rem sit et omnis nesciunt rerum cumque.",
    "float": 397.93000000000001,
    "nested": {
        "string": "Blanditiis numquam eligendi laboriosam eum eos excepturi. Sequi consequuntur voluptatibus sint sequi temporibus.",
        "float": 2364.8943223000001
    }
}',
            'getBusinessIdentifierCode'    => 'NQFMXPI4',
            'getCountryCode'               => 'MK',
            'getDateTimeSettableNoDefault' => 'DateTime',
            'getDateTimeSettableOnce'      => null,
            'isDefaultsDisabled'           => false,
            'isDefaultsEnabled'            => false,
            'isDefaultsNull'               => true,
            'getEmailAddress'              => 'yschroeder@example.net',
            'getEnum'                      => 'foo',
            'getIpAddress'                 => '192.168.41.14',
            'getIsbn'                      => '7561193033',
            'getLocaleIdentifier'          => 'se_FI',
            'getNullableString'            => 'THIS size: why, I should be like then?\' And she began fancying the sort of mixed flavour of.',
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
