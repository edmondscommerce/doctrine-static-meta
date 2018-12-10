<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\Numeric;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IndexedAutoIncrementFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Numeric\IndexedAutoIncrementFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String\BusinessIdentifierCodeFieldTrait
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\BusinessIdentifierCodeFakerData
 */
class IndexedAutoIncrementFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR           = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE
                                         . '/IndexedAutoIncrementFieldTraitTest/';
    protected const TEST_FIELD_FQN     = IndexedAutoIncrementFieldTrait::class;
    protected const TEST_FIELD_PROP    = IndexedAutoIncrementFieldInterface::PROP_INDEXED_AUTO_INCREMENT;
    protected const TEST_FIELD_DEFAULT = IndexedAutoIncrementFieldInterface::DEFAULT_INDEXED_AUTO_INCREMENT;
    protected const HAS_SETTER         = false;
    protected const VALIDATES          = false;

    public function setUp()
    {
        parent::setUp();
        $this->createDatabase();
    }


    /**
     * @test
     * @throws \Exception
     */
    public function createEntityWithField(): void
    {
        $this->persistThreeEntities();
        $loaded = $this->getRepositoryFactory()->getRepository($this->getEntityFqn())->findAll();
        list($entity1, $entity2, $entity3) = $loaded;
        $getter = $this->getGetter($entity1);
        self::assertTrue(\method_exists($entity1, $getter));
        $value1 = $entity1->$getter();
        $value2 = $entity2->$getter();
        $value3 = $entity3->$getter();
        self::assertInternalType('int', $value1);
        self::assertInternalType('int', $value2);
        self::assertInternalType('int', $value3);
        self::assertTrue($value2 > $value1);
        self::assertTrue($value3 > $value2);

    }

    private function persistThreeEntities(): void
    {
        $entity1 = $this->getEntity();
        $entity2 = $this->getEntity();
        $entity3 = $this->getEntity();
        $this->getEntitySaver()->save($entity1);
        $this->getEntitySaver()->save($entity2);
        $this->getEntitySaver()->save($entity3);
        $this->getEntityManager()->getUnitOfWork()->clear();
    }
}
