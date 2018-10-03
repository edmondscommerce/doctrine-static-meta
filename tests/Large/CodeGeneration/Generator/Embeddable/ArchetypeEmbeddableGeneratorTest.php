<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Financial\MoneyEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

// phpcs:disable
/**
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\ArchetypeEmbeddableGenerator
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter
 * @large
 */
// phpcs:enable
class ArchetypeEmbeddableGeneratorTest extends AbstractTest
{
    public const WORK_DIR = self::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/ArchetypeEmbeddableGeneratorIntegrationTest/';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;
    protected static $buildOnce = true;
    private          $testEntityFqn;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getTestCodeGenerator()
                 ->copyTo(self::WORK_DIR);
            self::$built = true;
        }
        $this->setupCopiedWorkDir();
        $this->testEntityFqn = $this->getCopiedFqn(self::TEST_ENTITY);
    }

    /**
     * @test
     * @large
     *      */
    public function itCanCreateAndEmbed(): void
    {
        $traitFqn = $this->getArchetypeEmbeddableGenerator()
                         ->createFromArchetype(
                             MoneyEmbeddable::class,
                             'PriceEmbeddable'
                         );

        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(
                 $this->testEntityFqn,
                 $traitFqn
             );
        self::assertTrue($this->qaGeneratedCode());
    }

    /**
     * @test
     * @large
     *      */
    public function itCanCreateAndEmbedMultipleTheSame(): void
    {
        $priceTraitFqn = $this->getArchetypeEmbeddableGenerator()
                              ->createFromArchetype(
                                  MoneyEmbeddable::class,
                                  'PriceEmbeddable'
                              );

        $costTraitFqn = $this->getArchetypeEmbeddableGenerator()
                             ->createFromArchetype(
                                 MoneyEmbeddable::class,
                                 'CostEmbeddable'
                             );


        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(
                 $this->testEntityFqn,
                 $priceTraitFqn
             );
        $this->getEntityEmbeddableSetter()
             ->setEntityHasEmbeddable(
                 $this->testEntityFqn,
                 $costTraitFqn
             );
        self::assertTrue($this->qaGeneratedCode());
    }
}
