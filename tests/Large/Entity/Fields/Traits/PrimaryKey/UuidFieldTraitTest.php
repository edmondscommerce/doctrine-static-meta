<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Ramsey\Uuid\UuidInterface;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class UuidFieldTraitTest extends IdFieldTraitTest
{
    public const    WORK_DIR        = AbstractTest::VAR_PATH .
                                      '/' .
                                      self::TEST_TYPE_LARGE .
                                      '/UuidFieldTraitTest/';
    protected const TEST_FIELD_FQN  = UuidFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;

    public function generateCode()
    {
        $this->getEntityGenerator()
             ->setUseUuidPrimaryKey(true)
             ->generateEntity(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
    }

    protected function validateSavedEntity($entity)
    {
        $id = $entity->getId();
        self::assertNotEmpty($id);
        self::assertInstanceOf(UuidInterface::class, $id);
    }
}
