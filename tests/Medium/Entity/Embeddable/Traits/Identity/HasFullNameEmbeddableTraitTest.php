<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

class HasFullNameEmbeddableTraitTest extends AbstractTest
{
    public const TEST_PROJECT_ROOT_NAMESPACE = 'HasFullNameEmbeddableTraitTest\\TestProject';
    public const WORK_DIR                    = AbstractTest::VAR_PATH . '/'
                                               . self::TEST_TYPE_MEDIUM . '/HasFullNameEmbeddableTraitTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;

    protected static $buildOnce = true;
    protected static $built     = false;
    private          $entity;

    public function setup()
    {
        parent::setUp();
        if (false === self::$built) {
            $this->generateTestCode();
        }
        $this->setupCopiedWorkDir();
        $entityFqn    = $this->getCopiedFqn(self::TEST_ENTITY);
        $this->entity = $this->createEntity($entityFqn);
    }

    /**
     * @test
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable\EntityEmbeddableSetter
     */
    public function generatedCodePassesQa(): void
    {
        self::assertTrue($this->qaGeneratedCode());
    }

    /**
     * @test
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait
     */
    public function theEmbeddableCanBeSettedAndGetted(): void
    {
        $expected = (new FullNameEmbeddable())->setFirstName('Rob');
        $this->entity->update(new class($expected) implements DataTransferObjectInterface
        {
            /**
             * @var FullNameEmbeddable
             */
            private $fullNameEmbeddable;

            /**
             *  constructor.
             */
            public function __construct(FullNameEmbeddable $fullNameEmbeddable)
            {
                $this->fullNameEmbeddable = $fullNameEmbeddable;
            }

            /**
             * @return FullNameEmbeddable
             */
            public function getFullNameEmbeddable(): FullNameEmbeddable
            {
                return $this->fullNameEmbeddable;
            }
        });
        $actual = $this->entity->getFullNameEmbeddable();
        self::assertSame($expected, $actual);
    }
}
