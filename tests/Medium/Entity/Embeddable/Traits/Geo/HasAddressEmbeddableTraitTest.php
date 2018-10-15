<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

class HasAddressEmbeddableTraitTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_MEDIUM . '/AddressEmbeddableTraitIntegrationTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;
    protected static $buildOnce = true;
    protected static $built     = false;
    private          $entity;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDir();
        $entityFqn    = $this->getCopiedFqn(self::TEST_ENTITY);
        $this->entity = $this->createEntity($entityFqn);
    }

    /**
     * @test
     * @medium
     * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait
     */
    public function theAddressEmbeddableCanBeSettedAndGetted(): void
    {
        $expected = (new AddressEmbeddable())->setCity('integration test town');
        $this->entity->update(new class($expected) implements DataTransferObjectInterface
        {
            /**
             * @var AddressEmbeddable
             */
            private $addressEmbeddable;

            /**
             *  constructor.
             */
            public function __construct(AddressEmbeddable $addressEmbeddable)
            {
                $this->addressEmbeddable = $addressEmbeddable;
            }

            /**
             * @return AddressEmbeddable
             */
            public function getAddressEmbeddable(): AddressEmbeddable
            {
                return $this->addressEmbeddable;
            }
        });
        $actual = $this->entity->getAddressEmbeddable();
        self::assertSame($expected, $actual);
    }
}
