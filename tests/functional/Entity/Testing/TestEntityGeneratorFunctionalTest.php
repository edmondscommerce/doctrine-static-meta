<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\FullProjectBuildFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class TestEntityGeneratorFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH .
                            '/' .
                            self::TEST_TYPE .
                            '/TestEntityGeneratorFunctionalTest';

    private const TEST_ENTITIES = FullProjectBuildFunctionalTest::TEST_ENTITIES;

    private const TEST_RELATIONS = FullProjectBuildFunctionalTest::TEST_RELATIONS;

    private const TEST_FIELD_FQN_BASE = FullProjectBuildFunctionalTest::TEST_FIELD_NAMESPACE_BASE . '\\Traits';

    public function testItCanGenerateASingleEntity(): EntityInterface
    {
        $entityFqn = current(self::TEST_ENTITIES);
        $this->getEntityGenerator()->generateEntity($entityFqn);
        $this->setupCopiedWorkDirAndCreateDatabase();
        $entityFqn           = $this->getCopiedFqn($entityFqn);
        $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
        $entityManager       = $this->getEntityManager();
        $entity              = $testEntityGenerator->generateEntity($entityManager, $entityFqn);
        $entityManager->persist($entity);
        $entityManager->flush();
        self::assertTrue(true);

        return $entity;
    }

    protected function getTestEntityGenerator(string $entityFqn): TestEntityGenerator
    {
        $testedEntityReflectionClass = new \ts\Reflection\ReflectionClass($entityFqn);

        return new TestEntityGenerator(
            AbstractEntityTest::SEED,
            [],
            $testedEntityReflectionClass,
            $this->container->get(EntitySaverFactory::class),
            $this->container->get(EntityValidatorFactory::class)
        );
    }

    /**
     * @param EntityInterface $originalEntity
     *
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @depends testItCanGenerateASingleEntity
     */
    public function testItCanGenerateAnOffsetEntity(EntityInterface $originalEntity)
    {
        $entityFqn           = \get_class($originalEntity);
        $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
        $entityManager       = $this->getEntityManager();
        $newEntity           = $testEntityGenerator->generateEntity($entityManager, $entityFqn, 1);
        self::assertNotEquals($newEntity->__toString(), $originalEntity->__toString());
    }

    public function testItGeneratesEntitiesAndAssociatedEntities(): void
    {
        $this->buildFullSuiteOfEntities();
        $entities      = [];
        $entityManager = $this->getEntityManager();
        $limit         = ($this->isQuickTests() ? 2 : null);
        foreach (self::TEST_ENTITIES as $key => $entityFqn) {
            if ($limit !== null && $key === $limit) {
                break;
            }
            $entityFqn           = $this->getCopiedFqn($entityFqn);
            $testEntityGenerator = $this->getTestEntityGenerator($entityFqn);
            $entity              = $testEntityGenerator->generateEntity($entityManager, $entityFqn);
            self::assertInstanceOf($entityFqn, $entity);
            $testEntityGenerator->addAssociationEntities($entityManager, $entity);
            $entities[] = $entity;
        }
        $this->getEntitySaver()->saveAll($entities);
        self::assertTrue(true);
    }

    protected function buildFullSuiteOfEntities(): void
    {
        $entityGenerator    = $this->getEntityGenerator();
        $fieldGenerator     = $this->getFieldGenerator();
        $relationsGenerator = $this->getRelationsGenerator();
        $fields             = [];
        foreach (MappingHelper::COMMON_TYPES as $type) {
            $fields[] = $fieldGenerator->generateField(
                self::TEST_FIELD_FQN_BASE . '\\' . ucwords($type),
                $type
            );
        }
        foreach (self::TEST_ENTITIES as $entityFqn) {
            $entityGenerator->generateEntity($entityFqn);
            foreach ($fields as $fieldFqn) {
                $this->getFieldSetter()->setEntityHasField($entityFqn, $fieldFqn);
            }
        }
        foreach (self::TEST_RELATIONS as $relation) {
            $relationsGenerator->setEntityHasRelationToEntity(...$relation);
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
    }

    public function testItCanGenerateMultipleEntities(): void
    {
        $this->buildFullSuiteOfEntities();
        $entityFqn = $this->getCopiedFqn(current(self::TEST_ENTITIES));
        $count     = $this->isQuickTests() ? 2 : 100;
        $actual    = $this->getTestEntityGenerator($entityFqn)->generateEntities(
            $this->getEntityManager(),
            $entityFqn,
            $count
        );
        self::assertCount($count, $actual);
        self::assertInstanceOf($entityFqn, current($actual));
    }
}
