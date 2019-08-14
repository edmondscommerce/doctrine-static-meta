<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\G\Entity\Testing\EntityGenerator;

use EdmondsCommerce\DoctrineStaticMeta\DoctrineStaticMeta;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFillerFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFillerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use function constant;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\FakerDataFiller
 * @large
 */
class FakerDataFillerTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/FakerDataFillerTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE .
                                TestCodeGenerator::TEST_ENTITY_PERSON;
    /**
     * @var string
     */
    private $testEntity;

    public function setUp()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
        $this->testEntity = $this->getCopiedFqn(self::TEST_ENTITY);
        $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @test
     */
    public function itCanFillADtoWithFakerData(): void
    {
        $dto = $this->getEntityDtoFactory()
                    ->createEmptyDtoFromEntityFqn($this->testEntity);
        $this->getFiller()->updateDtoWithFakeData($dto);
        // phpcs:disable
        self::assertSame(
            'Fugit est illo maiores cupiditate ea magni voluptatem. Doloribus ipsa et qui dolorem ut at voluptas. Quibusdam incidunt magnam et id veritatis.',
            $dto->getString()
        );
        // phpcs:enable
        self::assertSame(903863614, $dto->getInteger());
    }

    private function getFiller(): FakerDataFillerInterface
    {
        $dsm = $this->getDsmForTestEntity();

        $fakerDataProviders = constant(
            '\FakerDataFillerTest_ItCanFillADtoWithFakerData_\Entities\AbstractEntityTest::FAKER_DATA_PROVIDERS'
        );

        return $this->container->get(FakerDataFillerFactory::class)
                               ->setSeed(1000.0)
                               ->setFakerDataProviders($fakerDataProviders)
                               ->getInstanceFromDsm($dsm);
    }

    private function getDsmForTestEntity(): DoctrineStaticMeta
    {
        $entityFqn = $this->testEntity;

        return $entityFqn::getDoctrineStaticMeta();
    }
}
