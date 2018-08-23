<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\CountryCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UrlFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

// phpcs:disable
/**
 * Class ArchetypeEmbeddableGeneratorIntegrationTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Generator\Embeddable
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator
 */
// phpcs:enable
class ArchetypeEmbeddableGeneratorTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/ArchetypeEmbeddableGeneratorIntegrationTest/';

    private const TEST_ENTITY_PRODUCT = self::TEST_PROJECT_ROOT_NAMESPACE . '\\'
                                        . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\Product';

    private $productFqn;

    public function setUp(): void    {
        parent::setUp();
        $this->getEntityGenerator()
             ->generateEntity(self::TEST_ENTITY_PRODUCT);
        $this->getFieldSetter()
             ->setEntityHasField(self::TEST_ENTITY_PRODUCT, CountryCodeFieldTrait::class);
        $this->getFieldSetter()
             ->setEntityHasField(self::TEST_ENTITY_PRODUCT, UrlFieldTrait::class);

        $this->setupCopiedWorkDir();
        $this->productFqn = $this->getCopiedFqn(self::TEST_ENTITY_PRODUCT);
    }

    /**
     * @test
     * @large
     * @covers ::createFromArchetype
     */
    public function itCanCreateAndEmbed(): void
    {
        $traitFqn = $this->getArchetypeEmbeddableGenerator()
                         ->setProjectRootNamespace($this->copiedRootNamespace)
                         ->setPathToProjectRoot($this->copiedWorkDir)
                         ->createFromArchetype(
                             MoneyEmbeddable::class,
                             'PriceEmbeddable'
                         );

        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(
                 $this->productFqn,
                 $traitFqn
             );
        self::assertTrue($this->qaGeneratedCode());
    }

    /**
     * @test
     * @large
     * @covers ::createFromArchetype
     */
    public function itCanCreateAndEmbedMultipleTheSame(): void
    {
        $priceTraitFqn = $this->getArchetypeEmbeddableGenerator()
                              ->setProjectRootNamespace($this->copiedRootNamespace)
                              ->setPathToProjectRoot($this->copiedWorkDir)
                              ->createFromArchetype(
                                  MoneyEmbeddable::class,
                                  'PriceEmbeddable'
                              );

        $costTraitFqn = $this->getArchetypeEmbeddableGenerator()
                             ->setProjectRootNamespace($this->copiedRootNamespace)
                             ->setPathToProjectRoot($this->copiedWorkDir)
                             ->createFromArchetype(
                                 MoneyEmbeddable::class,
                                 'CostEmbeddable'
                             );


        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(
                 $this->productFqn,
                 $priceTraitFqn
             );
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(
                 $this->productFqn,
                 $costTraitFqn
             );
        self::assertTrue($this->qaGeneratedCode());
    }
}
