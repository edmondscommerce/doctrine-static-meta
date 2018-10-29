<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use Ramsey\Uuid\UuidInterface;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class NonBinaryUuidFieldTraitTest extends IdFieldTraitTest
{
    public const    WORK_DIR        = AbstractTest::VAR_PATH .
                                      '/' .
                                      self::TEST_TYPE_LARGE .
                                      '/NonBinaryUuidFieldTraitTest/';
    protected const TEST_FIELD_FQN  = NonBinaryUuidFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;
    protected const VALIDATES       = false;

    public function generateCode()
    {
        $this->getEntityGenerator()
             ->setPrimaryKeyType(IdTrait::NON_BINARY_UUID_TRAIT)
             ->generateEntity(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
    }

    protected function validateSavedEntity($entity)
    {
        $id = $entity->getId();
        self::assertNotEmpty($id);
        self::assertInstanceOf(UuidInterface::class, $id);
    }
}
