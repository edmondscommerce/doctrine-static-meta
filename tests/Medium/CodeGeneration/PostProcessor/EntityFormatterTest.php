<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\CodeGeneration\PostProcessor;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\EntityFormatter;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use ts\Reflection\ReflectionClass;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PostProcessor\EntityFormatter
 * @medium
 */
class EntityFormatterTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/' . self::TEST_TYPE_MEDIUM . '/EntityFormatterTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;

    private const ENTITY_FORMATTED = '<?php declare(strict_types=1);

namespace EntityFormatterTest_ItFormatsEntities_\Entities;
// phpcs:disable Generic.Files.LineLength.TooLong

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Attribute\HasWeightEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Financial\HasMoneyEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Fields\Traits\BooleanFieldTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Fields\Traits\DatetimeFieldTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Fields\Traits\DecimalFieldTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Fields\Traits\FloatFieldTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Fields\Traits\IntegerFieldTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Fields\Traits\JsonFieldTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Fields\Traits\StringFieldTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Fields\Traits\TextFieldTrait;
use EntityFormatterTest_ItFormatsEntities_\Entity\Interfaces\AllEmbeddableInterface;

// phpcs:enable
class AllEmbeddable implements 
    AllEmbeddableInterface
{
    /**
     * DSM Traits 
     */
    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidatedEntityTrait;
    use DSM\Traits\ImplementNotifyChangeTrackingPolicy;
    use DSM\Traits\AlwaysValidTrait;

    /**
     * DSM Fields 
     */
    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;

    /**
     * Fields 
     */
    use StringFieldTrait;
    use DatetimeFieldTrait;
    use FloatFieldTrait;
    use DecimalFieldTrait;
    use IntegerFieldTrait;
    use TextFieldTrait;
    use BooleanFieldTrait;
    use JsonFieldTrait;

    /**
     * Embeddables 
     */
    use HasMoneyEmbeddableTrait;
    use HasAddressEmbeddableTrait;
    use HasFullNameEmbeddableTrait;
    use HasWeightEmbeddableTrait;
}
';

    public function setUp()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
    }

    /**
     * @test
     * @throws \ReflectionException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public function itFormatsEntities(): void
    {
        $this->getEntityFormatter()->setPathToProjectRoot($this->copiedWorkDir)->run();
        $expected = self::ENTITY_FORMATTED;
        $actual   = \ts\file_get_contents(
            (new ReflectionClass($this->getCopiedFqn(self::TEST_ENTITY)))->getFileName()
        );
        self::assertSame($expected, $actual);
    }

    private function getEntityFormatter(): EntityFormatter
    {
        return $this->container->get(EntityFormatter::class);
    }
}
