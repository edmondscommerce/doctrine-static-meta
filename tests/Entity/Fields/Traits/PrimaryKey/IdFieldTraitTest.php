<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitTest;

class IdFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR        = AbstractTest::VAR_PATH.'/IdFieldTraitTest/';
    protected const TEST_FIELD_FQN  = IdFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;

    public function setup()
    {
        parent::setup();
        $this->entitySuffix = substr(static::class, strrpos(static::class, '\\') + 1);
        $this->getEntityGenerator()
             ->generateEntity(static::TEST_ENTITY_FQN_BASE.$this->entitySuffix);
        $this->setupCopiedWorkDir();
    }


    /**
     * Can't really do setters etc on ID fields
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testCreateEntityWithField(): void
    {
        $entityFqn = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE);
        $entity    = new $entityFqn();
        $getter    = $this->getGetter($entity);
        $this->assertTrue(\method_exists($entity, $getter));
        $value = $entity->$getter();
        $this->assertEmpty($value);
    }
}
