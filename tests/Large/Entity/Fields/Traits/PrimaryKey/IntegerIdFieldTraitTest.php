<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field\IdTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;

/**
 * @covers \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IntegerIdFieldTrait
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class IntegerIdFieldTraitTest extends IdFieldTraitTest
{
    public const    WORK_DIR        = AbstractTest::VAR_PATH .
                                      '/' .
                                      self::TEST_TYPE_LARGE .
                                      '/IntegerIdFieldTraitTest/';
    protected const TEST_FIELD_FQN  = IntegerIdFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;

    public function generateCode()
    {
        $this->getEntityGenerator()
             ->setPrimaryKeyType(IdTrait::INTEGER_ID_FIELD_TRAIT)
             ->generateEntity(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
    }

    protected function validateSavedEntity($entity)
    {
        $id = $entity->getId();
        self::assertNotEmpty($id);
        self::assertInternalType('int', $id);
    }
}
