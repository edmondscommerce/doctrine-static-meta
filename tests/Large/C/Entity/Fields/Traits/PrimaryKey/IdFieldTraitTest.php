<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Fields\Traits\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Fields\Traits\AbstractFieldTraitTest;

/**
 * @large
 */
class IdFieldTraitTest extends AbstractFieldTraitTest
{
    public const    WORK_DIR        = AbstractTest::VAR_PATH . '/' . self::TEST_TYPE_LARGE . '/IdFieldTraitTest/';
    protected const TEST_FIELD_FQN  = IdFieldTrait::class;
    protected const TEST_FIELD_PROP = IdFieldInterface::PROP_ID;
    protected const VALIDATES       = false;

    /**
     * @test
     */
    public function createEntityWithField(): void
    {
        $entityFqn = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
        $entity    = $this->createEntity($entityFqn);
        $getter    = $this->getGetter($entity);
        self::assertTrue(\method_exists($entity, $getter));
        $value = $entity->$getter();
        self::assertNotEmpty($value);
    }

    /**
     * @test
     */
    public function createDatabaseSchema()
    {
        $this->createDatabase();
        $entityFqn = $this->getCopiedFqn(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
        $entity    = $this->createEntity($entityFqn);
        $saver     = $this->container->get(EntitySaver::class);
        $saver->save($entity);
        $repository  = $this->getRepositoryFactory()->getRepository($entityFqn);
        $entities    = $repository->findAll();
        $savedEntity = current($entities);
        $this->validateSavedEntity($savedEntity);
    }

    protected function validateSavedEntity($entity)
    {
        $id = $entity->getId();
        self::assertNotEmpty($id);
    }

    protected function generateCode()
    {
        $this->getEntityGenerator()
             ->generateEntity(static::TEST_ENTITY_FQN_BASE . $this->entitySuffix);
    }
}
