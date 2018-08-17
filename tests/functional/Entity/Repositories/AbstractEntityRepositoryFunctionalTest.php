<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use EdmondsCommerce\DoctrineStaticMeta\AbstractFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\AbstractIntegrationTest;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\AbstractEntityTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\FullProjectBuildFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;

/**
 * Class AbstractEntityRepositoryFunctionalTest
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/working-with-objects.html#querying
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractEntityRepositoryFunctionalTest extends AbstractFunctionalTest
{
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH . '/'
                            . self::TEST_TYPE . '/AbstractEntityRepositoryFunctionalTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE . '\\Entities\\TestEntity';

    private const TEST_FIELD_FQN_BASE = FullProjectBuildFunctionalTest::TEST_FIELD_NAMESPACE_BASE . '\\Traits';

    private $fields = [];

    private $generatedEntities = [];

    /**
     * @var AbstractEntityRepository
     */
    private $repository;

    public function setup()
    {
        parent::setup();
        $this->generateCode();
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->generateAndSaveTestEntities();
        $this->repository = $this->getRepository();
    }

    protected function generateCode(): void
    {
        $entityGenerator = $this->getEntityGenerator();

        $entityGenerator->generateEntity(self::TEST_ENTITY_FQN);
        $fieldGenerator = $this->getFieldGenerator();
        foreach (MappingHelper::COMMON_TYPES as $type) {
            $this->fields[] = $fieldFqn = $fieldGenerator->generateField(
                self::TEST_FIELD_FQN_BASE . '\\' . ucwords($type),
                $type
            );
            $this->getFieldSetter()->setEntityHasField(self::TEST_ENTITY_FQN, $fieldFqn);
        }
    }

    protected function generateAndSaveTestEntities(): void
    {
        $entityGenerator         = new TestEntityGenerator(
            AbstractEntityTest::SEED,
            [],
            new  \ts\Reflection\ReflectionClass(self::TEST_ENTITY_FQN),
            new EntitySaverFactory(
                $this->getEntityManager(),
                new EntitySaver($this->getEntityManager()),
                new NamespaceHelper()
            ),
            new EntityValidatorFactory(new DoctrineCache(new ArrayCache()))
        );
        $this->generatedEntities = $entityGenerator->generateEntities(
            $this->getEntityManager(),
            $this->getCopiedFqn(self::TEST_ENTITY_FQN),
            $this->isQuickTests() ? 2 : 100
        );
        $saver                   = new EntitySaver($this->getEntityManager());
        $saver->saveAll($this->generatedEntities);
    }

    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository($this->getCopiedFqn(self::TEST_ENTITY_FQN));
    }

    public function testFind(): void
    {
        $expected = $this->generatedEntities[array_rand($this->generatedEntities)];
        $actual   = $this->repository->find($expected->getId());
        self::assertSame($expected, $actual);
    }

    public function testFindAll(): void
    {
        $expected = $this->generatedEntities;
        $actual   = $this->repository->findAll();
        self::assertSame($expected, $actual);
    }

    public function testFindBy(): void
    {
        foreach (MappingHelper::COMMON_TYPES as $key => $property) {
            $entity = $this->getEntityByKey($key);
            ;
            $getter   = $this->getGetterForType($property);
            $criteria = [$property => $entity->$getter()];
            $actual   = $this->repository->findBy($criteria);
            self::assertTrue($this->arrayContainsEntity($entity, $actual));
        }
    }

    protected function getGetterForType(string $type): string
    {
        $ucType = ucfirst($type);
        $getter = "get$ucType";
        if (MappingHelper::TYPE_BOOLEAN === $type) {
            $getter = "is$ucType";
        }

        return $getter;
    }

    protected function arrayContainsEntity(EntityInterface $expectedEntity, array $array): bool
    {
        foreach ($array as $entity) {
            if ($entity->getId() === $expectedEntity->getId()) {
                return true;
            }
        }

        return false;
    }

    private function getEntityByKey(int $key): EntityInterface
    {
        if ($this->isQuickTests()) {
            return $this->generatedEntities[0];
        }

        return $this->generatedEntities[$key];
    }

    public function testFindOneBy(): void
    {
        foreach (MappingHelper::COMMON_TYPES as $key => $property) {
            $entity   = $this->getEntityByKey($key);
            $getter   = $this->getGetterForType($property);
            $value    = $entity->$getter();
            $criteria = [
                $property => $value,
                'id'      => $entity->getId(),
            ];
            $actual   = $this->repository->findOneBy($criteria);
            self::assertEquals(
                $entity,
                $actual,
                'Failed finding one expected entity (ID' . $entity->getId() . ') with $criteria: '
                . "\n" . var_export($criteria, true)
                . "\n and \$actual: "
                . "\n" . (new EntityDebugDumper())->dump($actual, $this->getEntityManager())
            );
        }
    }

    public function testGetClassName(): void
    {
        self::assertSame(
            ltrim($this->getCopiedFqn(self::TEST_ENTITY_FQN), '\\'),
            $this->repository->getClassName()
        );
    }

    public function testMatching(): void
    {
        foreach (MappingHelper::COMMON_TYPES as $key => $property) {
            $entity = $this->getEntityByKey($key);
            ;
            $getter   = $this->getGetterForType($property);
            $value    = $entity->$getter();
            $criteria = new Criteria();
            $criteria->where(new Comparison($property, '=', $value));
            $criteria->andWhere(new Comparison('id', '=', $entity->getId()));
            $actual = $this->repository->matching($criteria);
            self::assertTrue($this->collectionContainsEntity($entity, $actual));
        }
    }

    protected function collectionContainsEntity(EntityInterface $expectedEntity, Collection $collection): bool
    {
        foreach ($collection->getIterator() as $entity) {
            if ($entity->getId() === $expectedEntity->getId()) {
                return true;
            }
        }

        return false;
    }

    public function testCreateQueryBuilder(): void
    {
        $this->repository->createQueryBuilder('foo');
        self::assertTrue(true);
    }

    public function testCreateResultSetMappingBuilder(): void
    {
        $this->repository->createResultSetMappingBuilder('foo');
        self::assertTrue(true);
    }

    public function testCreateNamedQuery(): void
    {
        $this->markTestIncomplete(
            'Need to add a named query for a test entity somehow in the meta data before we can test this'
        );
        $this->repository->createNamedQuery('foo');
        self::assertTrue(true);
    }

    public function testClear(): void
    {
        $this->repository->clear();
        self::assertSame(
            ['AbstractEntityRepositoryFunctionalTest_testClear_\Entities\TestEntity' => []],
            $this->getEntityManager()->getUnitOfWork()->getIdentityMap()
        );
    }

    public function testCount(): void
    {
        self::assertSame($this->isQuickTests() ? 2 : 100, $this->repository->count([]));
    }
}
