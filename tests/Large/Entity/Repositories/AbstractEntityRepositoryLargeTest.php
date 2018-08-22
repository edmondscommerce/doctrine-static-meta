<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\Entity\Repositories;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaverFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\AbstractEntityTest;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Large\FullProjectBuildLargeTest;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;

/**
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/working-with-objects.html#querying
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @large
 * @coversDefaultClass \EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository
 */
class AbstractEntityRepositoryLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE . '/AbstractEntityRepositoryLargeTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE
                                    . '\\Entities\\AbstractEntityRepositoryLargeTestEntity';

    private const TEST_FIELD_FQN_BASE = FullProjectBuildLargeTest::TEST_FIELD_NAMESPACE_BASE . '\\Traits';

    private const NUM_ENTITIES_QUICK = 2;

    private const NUM_ENTITIES_FULL = 10;
    protected static $buildOnce         = true;
    private $fields            = [];
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
        if (true === self::$built) {
            return;
        }
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
        self::$built = true;
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
            $this->isQuickTests() ? self::NUM_ENTITIES_QUICK : self::NUM_ENTITIES_FULL
        );
        $saver                   = new EntitySaver($this->getEntityManager());
        $saver->saveAll($this->generatedEntities);
    }

    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository($this->getCopiedFqn(self::TEST_ENTITY_FQN));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::initRepository
     * @covers ::getEntityFqn
     */
    public function loadWithNullMetaData(): void
    {
        $repo    = $this->getRepository();
        $repoFqn = \get_class($repo);
        new $repoFqn($this->getEntityManager());
        self::assertTrue(true);
    }

    /**
     * @test
     * @covers ::find
     * @covers ::__construct
     */
    public function find(): void
    {
        $expected = $this->generatedEntities[array_rand($this->generatedEntities)];
        $actual   = $this->repository->find($expected->getId());
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers ::findAll
     */
    public function findAll(): void
    {
        $expected = $this->generatedEntities;
        $actual   = $this->repository->findAll();
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     * @covers ::findBy
     */
    public function findBy(): void
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

    private function getEntityByKey(int $key): EntityInterface
    {
        if ($this->isQuickTests()) {
            return $this->generatedEntities[0];
        }

        return $this->generatedEntities[$key];
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

    /**
     * @test
     * @covers ::findOneBy
     */
    public function findOneBy(): void
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

    /**
     * @test
     * @covers ::getClassName
     */
    public function getClassName(): void
    {
        self::assertSame(
            ltrim($this->getCopiedFqn(self::TEST_ENTITY_FQN), '\\'),
            $this->repository->getClassName()
        );
    }

    /**
     * @test
     * @covers ::matching
     */
    public function matching(): void
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

    /**
     * @test
     * @covers ::createQueryBuilder
     */
    public function createQueryBuilder(): void
    {
        $this->repository->createQueryBuilder('foo');
        self::assertTrue(true);
    }

    /**
     * @test
     * @covers ::createResultSetMappingBuilder
     */
    public function createResultSetMappingBuilder(): void
    {
        $this->repository->createResultSetMappingBuilder('foo');
        self::assertTrue(true);
    }

    /**
     * @test
     * @covers ::createNamedQuery
     */
    public function createNamedQuery(): void
    {
        $this->markTestIncomplete(
            'Need to add a named query for a test entity somehow in the meta data before we can test this'
        );
        $this->repository->createNamedQuery('foo');
        self::assertTrue(true);
    }

    /**
     * @test
     * @covers ::clear
     */
    public function clear(): void
    {
        $this->repository->clear();
        self::assertSame(
            ['AbstractEntityRepositoryLargeTest_clear_\Entities\AbstractEntityRepositoryLargeTestEntity' => []],
            $this->getEntityManager()->getUnitOfWork()->getIdentityMap()
        );
    }

    /**
     * @covers ::count
     */
    public function testCount(): void
    {
        self::assertSame(
            $this->isQuickTests() ? self::NUM_ENTITIES_QUICK : self::NUM_ENTITIES_FULL,
            $this->repository->count([])
        );
    }
}
