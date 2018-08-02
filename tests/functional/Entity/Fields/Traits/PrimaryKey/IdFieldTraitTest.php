<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;

class IdFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR        = AbstractIntegrationTest::VAR_PATH . '/' . self::TEST_TYPE . '/IdFieldTraitTest/';
    protected const TEST_FIELD_FQN  = IdFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;

    /**
     * Can't really do setters etc on ID fields
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testCreateEntityWithField(): void
    {
        $this->setupCopiedWorkDir();
        $entityFqn = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
        $entity    = $this->createEntity($entityFqn);
        $getter    = $this->getGetter($entity);
        self::assertTrue(\method_exists($entity, $getter));
        $value = $entity->$getter();
        self::assertEmpty($value);
    }

    public function testCreateDatabaseSchema()
    {
        $this->setupCopiedWorkDirAndCreateDatabase();
        $entityFqn     = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
        $entity        = $this->createEntity($entityFqn);
        $saver         = $this->container->get(EntitySaver::class);
        $saver->save($entity);
        $repository  = $this->getEntityRepository($entityFqn);
        $entities    = $repository->findAll();
        $savedEntity = current($entities);
        $this->validateSavedEntity($savedEntity);
    }

    protected function validateSavedEntity($entity)
    {
        $id = $entity->getId();
        self::assertNotEmpty($id);
        self::assertInternalType('numeric', $id);
    }

    protected function generateCode()
    {
        $this->getEntityGenerator()
             ->generateEntity(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
    }
}
