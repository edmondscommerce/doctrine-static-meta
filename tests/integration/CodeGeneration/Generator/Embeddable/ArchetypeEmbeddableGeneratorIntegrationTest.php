<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\CountryCodeFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\UrlFieldTrait;

class ArchetypeEmbeddableGeneratorIntegrationTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'
                            .self::TEST_TYPE.'/ArchetypeEmbeddableGeneratorIntegrationTest/';

    private const TEST_ENTITY_PRODUCT = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                        .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Product';

    private const TEST_EMBEDDED_PRICE_CLASSNAME = 'PriceEmbedded';

    private $built = false;
    private $productFqn;

    public function setup()
    {
        parent::setup();
        if (false === $this->built) {
            $this->getEntityGenerator()
                 ->generateEntity(self::TEST_ENTITY_PRODUCT);
            $this->getFieldSetter()
                 ->setEntityHasField(self::TEST_ENTITY_PRODUCT, CountryCodeFieldTrait::class);
            $this->getFieldSetter()
                 ->setEntityHasField(self::TEST_ENTITY_PRODUCT, UrlFieldTrait::class);
            $this->built = true;
        }
        $this->setupCopiedWorkDir();
        $this->productFqn = $this->getCopiedFqn(self::TEST_ENTITY_PRODUCT);
    }

    /**
     * @test
     * @testdox You can create a PriceEmbedded from the Money archetype and then assign it to an Entity and its valid
     * @medium
     */
    public function itCanCreateAndEmbedd()
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
        $this->assertTrue($this->qaGeneratedCode());
    }

    /**
     * @test
     * @testdox You can create a PriceEmbedded from the Money archetype and then assign it to an Entity and its valid
     * @medium
     */
    public function itCanCreateAndEmbedMultipleTheSame()
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
        $this->assertTrue($this->qaGeneratedCode());
    }
}
