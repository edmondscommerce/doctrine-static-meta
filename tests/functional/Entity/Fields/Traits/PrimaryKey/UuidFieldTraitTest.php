<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;

class UuidFieldTraitTest extends IdFieldTraitTest
{
    public const    WORK_DIR        = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/UuidFieldTraitTest/';
    protected const TEST_FIELD_FQN  = UuidFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;

    protected const UUID_REGEX =
        '/^(\{{0,1}([0-9a-fA-F]){8}-([0-9a-fA-F]){4}-([0-9a-fA-F]){4}-([0-9a-fA-F]){4}-([0-9a-fA-F]){12}\}{0,1})$/i';

    public function generateCode()
    {
        $this->getEntityGenerator()
             ->setUseUuidPrimaryKey(true)
             ->generateEntity(static::TEST_ENTITY_FQN_BASE.$this->entitySuffix);
        $this->setupCopiedWorkDir();
    }

    protected function validateSavedEntity($entity)
    {
        $id = $entity->getId();
        $this->assertNotEmpty($id);
        $this->assertRegExp(self::UUID_REGEX, $id);
    }
}
