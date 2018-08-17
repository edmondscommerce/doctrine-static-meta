<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

class HasFullNameEmbeddableTraitTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE . '/HasFullNameEmbeddableTraitTest';

    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Person';
    protected static $buildOnce = true;
    private $entity;

    public function setup()
    {
        parent::setup();
        if (false === self::$built) {
            $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
            $this->getEntityEmbeddableSetter()
                 ->setEntityHasEmbeddable(self::TEST_ENTITY, HasFullNameEmbeddableTrait::class);
            self::$built = true;
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
        $this->entity->setFullNameEmbeddable($expected);
        $actual = $this->entity->getFullNameEmbeddable();
        self::assertSame($expected, $actual);
    }
}
