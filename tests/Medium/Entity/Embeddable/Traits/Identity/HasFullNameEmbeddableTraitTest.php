<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Medium\Entity\Embeddable\Traits\Identity;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Identity\FullNameEmbeddable;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @medium
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Identity\HasFullNameEmbeddableTrait
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class HasFullNameEmbeddableTraitTest extends AbstractTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_MEDIUM . '/HasFullNameEmbeddableTraitTest';

    private const TEST_ENTITY = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ALL_EMBEDDABLES;

    protected static $buildOnce = true;
    protected static $built     = false;
    private $entity;

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
    public function theEmbeddableCanBeSettedAndGetted(): void
    {
        $expected = (new FullNameEmbeddable())->setFirstName('Rob');
        $this->entity->update(new class($expected, $this->entity->getId()) implements DataTransferObjectInterface
        {
            /**
             * @var FullNameEmbeddable
             */
            private $fullNameEmbeddable;
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
            public function __construct(FullNameEmbeddable $fullNameEmbeddable, UuidInterface $id)
            {
                $this->fullNameEmbeddable = $fullNameEmbeddable;
                $this->id                 = $id;
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
