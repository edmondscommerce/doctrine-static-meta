<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Tests\Large\C\Entity\Repositories;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\RepositoryFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityDebugDumper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGenerator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Testing\EntityGenerator\TestEntityGeneratorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractLargeTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\AbstractTest;
use EdmondsCommerce\DoctrineStaticMeta\Tests\Assets\TestCodeGenerator;

/**
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/working-with-objects.html#querying
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @large
 * @covers  \EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository
 */
class AbstractEntityRepositoryLargeTest extends AbstractLargeTest
{
    public const WORK_DIR = AbstractTest::VAR_PATH . '/'
                            . self::TEST_TYPE_LARGE . '/AbstractEntityRepositoryLargeTest';

    private const PERSON_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_PERSON;
    private const ADDRESS_ENTITY_FQN = self::TEST_ENTITIES_ROOT_NAMESPACE . TestCodeGenerator::TEST_ENTITY_ATTRIBUTES_ADDRESS;

    private const NUM_ENTITIES_QUICK = 20;

    private const NUM_ENTITIES_FULL = 100;

    protected static $buildOnce = true;

    private $generatedEntities = [];
    /**
     * @var AbstractEntityRepository
     */
    private $repository;

    public function setup()
    {
        parent::setUp();
        $this->generateTestCode();
        $this->setupCopiedWorkDirAndCreateDatabase();
        $this->generateAndSaveTestEntities();
        $this->repository = $this->getRepository();
    }

    protected function generateAndSaveTestEntities(): void
    {
        /**
         * @var TestEntityGenerator $entityGenerator
         */
        $entityGenerator         =
            $this->container->get(TestEntityGeneratorFactory::class)
                            ->createForEntityFqn($this->getCopiedFqn(self::PERSON_ENTITY_FQN));
        $this->generatedEntities = $entityGenerator->generateEntities(
            $this->isQuickTests() ? self::NUM_ENTITIES_QUICK : self::NUM_ENTITIES_FULL
        );
        foreach ($this->generatedEntities as $entity) {
            $entityGenerator->addAssociationEntities($entity);
        }
        $saver                   = new EntitySaver($this->getEntityManager());
        $saver->saveAll($this->generatedEntities);
    }

    protected function getRepository(): AbstractEntityRepository
    {
        return $this->container->get(RepositoryFactory::class)
                               ->getRepository($this->getCopiedFqn(self::PERSON_ENTITY_FQN));
    }


    /**
     * @test
     */
    public function find(): void
    {
        $expected = $this->generatedEntities[array_rand($this->generatedEntities)];
        $actual   = $this->repository->find($expected->getId());
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function get(): void
    {
        $expected = $this->generatedEntities[array_rand($this->generatedEntities)];
        $actual   = $this->repository->get($expected->getId());
        self::assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function itCanUseEntitiesInDql(): void
    {
        $queryBuilder = $this->repository->createQueryBuilder('fetch');
        $queryBuilder->where('fetch.attributesAddress IS NOT NULL');

        $person = $queryBuilder->getQuery()->execute()[0];
        $address = $person->getAttributesAddress();
        $secondQuery = $this->repository->createQueryBuilder('second');
        $secondQuery->where('second.attributesAddress = :address');
        $secondQuery->setParameter('address', $address);
        $fetchedAddress = $secondQuery->getQuery()->execute()[0];

        self::assertSame($address->getId()->toString(), $fetchedAddress->getId()->toString());
    }


    /**
     * @test
     */
    public function getWillThrowAnExceptionIfNothingIsFound(): void
    {
        $this->expectException(DoctrineStaticMetaException::class);
        $this->repository->get(time());
    }

    /**
     * @test
     */
    public function findAll(): void
    {
        $expected = $this->sortCollectionById($this->generatedEntities);
        $actual   = $this->sortCollectionById($this->repository->findAll());
        self::assertEquals($expected, $actual);
    }

    private function sortCollectionById(array $collection): array
    {
        $return = [];
        foreach ($collection as $item) {
            $return[(string)$item->getId()] = $item;
        }
        ksort($return);

        return $return;
    }

    /**
     * @test
     */
    public function findBy(): void
    {
        foreach (MappingHelper::COMMON_TYPES as $property) {
            $entity   = current($this->generatedEntities);
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

    /**
     * @test
     */
    public function findOneBy(): void
    {
        foreach (MappingHelper::COMMON_TYPES as $property) {
            $entity   = current($this->generatedEntities);
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
     */
    public function getOneBy(): void
    {
        $entity   = current($this->generatedEntities);
        $getter   = $this->getGetterForType(MappingHelper::TYPE_STRING);
        $value    = $entity->$getter();
        $criteria = [
            MappingHelper::TYPE_STRING => $value,
            'id'                       => $entity->getId(),
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

    /**
     * @test
     */
    public function getOneByWillThrowAnExceptionIfNothingIsFound(): void
    {
        $property = MappingHelper::TYPE_STRING;
        $criteria = [$property => 'not-a-real-vaule'];
        $this->expectException(\RuntimeException::class);
        $this->repository->getOneBy($criteria);
    }

    /**
     * @test
     */
    public function getClassName(): void
    {
        self::assertSame(
            ltrim($this->getCopiedFqn(self::PERSON_ENTITY_FQN), '\\'),
            $this->repository->getClassName()
        );
    }

    /**
     * @test
     */
    public function matching(): void
    {
        foreach (MappingHelper::COMMON_TYPES as $property) {
            $entity   = current($this->generatedEntities);
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
     */
    public function createQueryBuilder(): void
    {
        $this->repository->createQueryBuilder('foo');
        self::assertTrue(true);
    }

    /**
     * @test
     */
    public function createResultSetMappingBuilder(): void
    {
        $this->repository->createResultSetMappingBuilder('foo');
        self::assertTrue(true);
    }

    /**
     * @test
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
     */
    public function clear(): void
    {
        $this->repository->clear();
        $map = $this->getEntityManager()->getUnitOfWork()->getIdentityMap();
        self::assertSame(
            [],
            $map[ltrim($this->getCopiedFqn(self::PERSON_ENTITY_FQN), '\\')]
        );
    }

    /**
     */
    public function testCount(): void
    {
        self::assertSame(
            $this->isQuickTests() ? self::NUM_ENTITIES_QUICK : self::NUM_ENTITIES_FULL,
            $this->repository->count([])
        );
    }

    /**
     * @test
     */
    public function getRandomBy(): void
    {
        $criteria = [];
        $result   = $this->repository->getRandomBy($criteria);
        self::assertCount(1, $result);
        $result = $this->repository->getRandomBy($criteria, 2);
        self::assertCount(2, $result);
    }

    /**
     * @test
     */
    public function getRandomOneBy(): void
    {
        $criteria = [];
        $tries    = 0;
        $maxTries = 3;
        while ($tries++ < $maxTries) {
            $rand1 = $this->repository->getRandomOneBy($criteria);
            $rand2 = $this->repository->getRandomOneBy($criteria);
            if ($rand1 !== $rand2) {
                self::assertTrue(true);
                return;
            }
        }
        $this->fail('Failed pulling out two random entities that were not the same');
    }
}
