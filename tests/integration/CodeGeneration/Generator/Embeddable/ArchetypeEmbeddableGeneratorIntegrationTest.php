<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\NameFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute\QtyFieldTrait;

class ArchetypeEmbeddableGeneratorIntegrationTest extends AbstractIntegrationTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'
                            .self::TEST_TYPE.'/ArchetypeEmbeddableGeneratorIntegrationTest/';

    private const TEST_ENTITY_PRODUCT = self::TEST_PROJECT_ROOT_NAMESPACE.'\\'
                                        .AbstractGenerator::ENTITIES_FOLDER_NAME.'\\Product';

    private const TEST_EMBEDDED_PRICE_CLASSNAME = 'PriceEmbedded';
    private const TEST_EMBEDDED_PRICE_FQN       = self::TEST_PROJECT_ROOT_NAMESPACE
                                                  .'\\Entity\\Embedded\\Financial\\'
                                                  .self::TEST_EMBEDDED_PRICE_CLASSNAME;

    private $built = false;
    private $productFqn;

    public function setup()
    {
        parent::setup();
        if (false === $this->built) {
            $this->getEntityGenerator()
                 ->generateEntity(self::TEST_ENTITY_PRODUCT);
            $this->getFieldSetter()
                 ->setEntityHasField(self::TEST_ENTITY_PRODUCT, NameFieldTrait::class);
            $this->getFieldSetter()
                 ->setEntityHasField(self::TEST_ENTITY_PRODUCT, QtyFieldTrait::class);
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
    public function createAssign()
    {
        $traitFqn = $this->getArchetypeEmbeddableGenerator()
                         ->setProjectRootNamespace($this->copiedRootNamespace)
                         ->setPathToProjectRoot($this->copiedWorkDir)
                         ->createFromArchetype(
                             MoneyEmbeddable::class,
                             'PriceEmbeddable'
                         );
//        $this->assertTrue(
//            \class_exists($traitFqn),
//            'Failed finding trait FQN ending '
//            .\str_replace($this->copiedRootNamespace, '...', $traitFqn)
//        );

        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(
                 $this->productFqn,
                 $traitFqn
             );
        $this->assertTrue($this->qaGeneratedCode());
    }
}
