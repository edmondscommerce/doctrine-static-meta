<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\AbstractFieldTraitFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;

class IdFieldTraitTest extends AbstractFieldTraitFunctionalTest
{
    public const    WORK_DIR        = AbstractIntegrationTest::VAR_PATH.'/'.self::TEST_TYPE.'/IdFieldTraitTest/';
    protected const TEST_FIELD_FQN  = IdFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;

    protected function generateCode()
    {
        $this->getEntityGenerator()
             ->generateEntity(static::TEST_ENTITY_FQN_BASE.$this->entitySuffix);
    }


    /**
     * Can't really do setters etc on ID fields
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function testCreateEntityWithField(): void
    {
        $this->setupCopiedWorkDir();
        $entityFqn = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE.$this->entitySuffix);
        $entity    = new $entityFqn();
        $getter    = $this->getGetter($entity);
        $this->assertTrue(\method_exists($entity, $getter));
        $value = $entity->$getter();
        $this->assertEmpty($value);
    }

    public function testCreateDatabaseSchema()
    {
        $this->setupCopiedWorkDirAndCreateDatabase();
        $entityManager = $this->getEntityManager();
        $entityFqn     = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE.$this->entitySuffix);
        $entity        = new $entityFqn();
        $saver         = $this->container->get(EntitySaver::class);
        $saver->save($entity);
        $repository  = $entityManager->getRepository($entityFqn);
        $entities    = $repository->findAll();
        $savedEntity = current($entities);
        $this->validateSavedEntity($savedEntity);
    }

    protected function validateSavedEntity($entity)
    {
        $id = $entity->getId();
        $this->assertNotEmpty($id);
        $this->assertTrue(is_numeric($id));
    }
}
