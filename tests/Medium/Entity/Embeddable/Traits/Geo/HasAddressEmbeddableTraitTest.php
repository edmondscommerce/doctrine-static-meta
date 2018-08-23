<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

class HasAddressEmbeddableTraitTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_MEDIUM . '/AddressEmbeddableTraitIntegrationTest';

    private const TEST_ENTITY = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\Place';
    /**
     * @var bool
     */
    protected static $buildOnce = true;
    /**
     * @var EntityInterface
     */
    private $entity;

    public function setUp(): void
    {
        parent::setUp();
        if (false === self::$built) {
            $this->getEntityGenerator()->generateEntity(self::TEST_ENTITY);
            $this->getEntityEmbeddableSetter()
                 ->setEntityHasEmbeddable(self::TEST_ENTITY, HasAddressEmbeddableTrait::class);
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
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait
     */
    public function theAddressEmbeddableCanBeSettedAndGetted(): void
    {
        $expected = (new AddressEmbeddable())->setCity('integration test town');
        $this->entity->setAddressEmbeddable($expected);
        $actual = $this->entity->getAddressEmbeddable();
        self::assertSame($expected, $actual);
    }
}
