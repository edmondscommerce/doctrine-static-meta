<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @medium
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo\HasAddressEmbeddableTrait
 */
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
     */
    public function theAddressEmbeddableCanBeSettedAndGetted(): void
    {
        $expected = (new AddressEmbeddable())->setCity('integration test town');
        $this->entity->update(new class($expected, $this->entity->getId()) implements DataTransferObjectInterface
        {
            /**
             * @var AddressEmbeddable
             */
            private $addressEmbeddable;
            /**
             * @var UuidInterface
             */
            private $id;

            public static function getEntityFqn(): string
            {
                return 'Entity\\Fqn';
            }

            public function getId(): UuidInterface
            {
                return $this->id;
            }

            /**
             *  constructor.
             */
            public function __construct(AddressEmbeddable $addressEmbeddable, UuidInterface $id)
            {
                $this->addressEmbeddable = $addressEmbeddable;
                $this->id                = $id;
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
