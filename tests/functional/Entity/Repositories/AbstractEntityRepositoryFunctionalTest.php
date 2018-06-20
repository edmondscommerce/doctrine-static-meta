<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

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
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\FullProjectBuildFunctionalTest;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

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
    public const WORK_DIR = AbstractIntegrationTest::VAR_PATH.'/'
                            .self::TEST_TYPE.'/AbstractEntityRepositoryFunctionalTest';

    private const TEST_ENTITY_FQN = self::TEST_PROJECT_ROOT_NAMESPACE.'\\Entities\\TestEntity';

    private const TEST_FIELD_FQN_BASE = FullProjectBuildFunctionalTest::TEST_FIELD_NAMESPACE_BASE.'\\Traits';

    private $built = false;

    private $fields = [];

    private $generatedEntities = [];

    /**
     * @var AbstractEntityRepository
     */
    private $repository;

    public function setup()
    {
        parent::setup();
        if (true !== $this->built) {
            $this->generateCode();
            $this->built = true;
        }
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->generateAndSaveTestEntities();
        $this->repository = $this->getRepository();
        $this->built      = true;
    }

    protected function generateCode()
    {
        $entityGenerator = $this->getEntityGenerator();

        $entityGenerator->generateEntity(self::TEST_ENTITY_FQN);
        $fieldGenerator = $this->getFieldGenerator();
        foreach (MappingHelper::COMMON_TYPES as $type) {
            $this->fields[] = $fieldFqn = $fieldGenerator->generateField(
                self::TEST_FIELD_FQN_BASE.'\\'.ucwords($type),
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
            new \ReflectionClass(self::TEST_ENTITY_FQN),
            new EntitySaverFactory(
                $this->getEntityManager(),
                new EntitySaver($this->getEntityManager()),
                new NamespaceHelper()
            )
        );
        $this->generatedEntities = $entityGenerator->generateEntities(
            $this->getEntityManager(),
            $this->getCopiedFqn(self::TEST_ENTITY_FQN),
            100
        );
        $saver                   = new EntitySaver($this->getEntityManager());
        $saver->saveAll($this->generatedEntities);
    }

    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository($this->getCopiedFqn(self::TEST_ENTITY_FQN));
    }

    public function testFind()
    {
        $expected = $this->generatedEntities[array_rand($this->generatedEntities)];
        $actual   = $this->repository->find($expected->getId());
        $this->assertSame($expected, $actual);
    }

    public function testFindAll()
    {
        $expected = $this->generatedEntities;
        $actual   = $this->repository->findAll();
        $this->assertSame($expected, $actual);
    }

    public function testFindBy()
    {
        foreach (MappingHelper::COMMON_TYPES as $key => $property) {
            $entity   = $this->generatedEntities[$key];
            $getter   = $this->getGetterForType($property);
            $criteria = [$property => $entity->$getter()];
            $actual   = $this->repository->findBy($criteria);
            $this->assertTrue($this->arrayContainsEntity($entity, $actual));
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

    public function testFindOneBy(): void
    {
        foreach (MappingHelper::COMMON_TYPES as $key => $property) {
            $entity   = $this->generatedEntities[$key];
            $getter   = $this->getGetterForType($property);
            $value    = $entity->$getter();
            $criteria = [
                $property => $value,
                'id'      => $entity->getId(),
            ];
            $actual   = $this->repository->findOneBy($criteria);
            $this->assertEquals(
                $entity,
                $actual,
                'Failed finding one expected entity (ID'.$entity->getId().') with $criteria: '
                ."\n".var_export($criteria, true)
                ."\n and \$actual: "
                ."\n".var_export($actual, true)
            );
        }
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

    public function testGetClassName()
    {
        $this->assertSame(
            ltrim($this->getCopiedFqn(self::TEST_ENTITY_FQN), '\\'),
            $this->repository->getClassName()
        );
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

    public function testMatching()
    {
        foreach (MappingHelper::COMMON_TYPES as $key => $property) {
            $entity   = $this->generatedEntities[$key];
            $getter   = $this->getGetterForType($property);
            $value    = $entity->$getter();
            $criteria = new Criteria();
            $criteria->where(new Comparison($property, '=', $value));
            $criteria->andWhere(new Comparison('id', '=', $entity->getId()));
            $actual = $this->repository->matching($criteria);
            $this->assertTrue($this->collectionContainsEntity($entity, $actual));
        }
    }

    public function testCreateQueryBuilder()
    {
        $this->repository->createQueryBuilder('foo');
        $this->assertTrue(true);
    }

    public function testCreateResultSetMappingBuilder()
    {
        $this->repository->createResultSetMappingBuilder('foo');
        $this->assertTrue(true);
    }

    public function testCreateNamedQuery()
    {
        $this->markTestIncomplete(
            'Need to add a named query for a test entity somehow in the meta data before we can test this'
        );
        $this->repository->createNamedQuery('foo');
        $this->assertTrue(true);
    }

    public function testClear()
    {
        $this->repository->clear();
        $this->assertSame(
            ['AbstractEntityRepositoryFunctionalTest_testClear_\Entities\TestEntity' => []],
            $this->getEntityManager()->getUnitOfWork()->getIdentityMap()
        );
        $this->built = false;
    }

    public function testCount()
    {
        $this->assertSame(100, $this->repository->count([]));
    }
}
