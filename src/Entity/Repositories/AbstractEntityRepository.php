<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

use function str_replace;

/**
 * Class AbstractEntityRepository
 *
 * This provides a base class that handles instantiating the correctly configured EntityRepository and provides an
 * extensible baseline for further customisation
 *
 * We have extracted an interface from the standard Doctrine EntityRepository and implemented that
 * However, so we can add type safety, we can't "actually" implement it
 *
 * We have also deliberately left out the magic calls. Please make real methods in your concrete repository class
 *
 * Note, there are quite a few PHPMD warnings, however it needs to respect the legacy interface so they are being
 * suppressed
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractEntityRepository implements EntityRepositoryInterface
{
    /**
     * @var array
     */
    protected static $aliasCache;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var EntityRepository
     */
    protected $entityRepository;
    /**
     * @var string
     */
    protected $repositoryFactoryFqn;
    /**
     * @var ClassMetadata|null
     */
    protected $metaData;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var EntityFactoryInterface
     */
    private $entityFactory;

    /**
     * AbstractEntityRepositoryFactory constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EntityFactoryInterface $entityFactory
     * @param NamespaceHelper|null   $namespaceHelper
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityFactoryInterface $entityFactory,
        NamespaceHelper $namespaceHelper
    ) {
        $this->entityManager   = $entityManager;
        $this->namespaceHelper = $namespaceHelper;
        $this->entityFactory   = $entityFactory;
        $this->initRepository();
    }

    protected function initRepository(): void
    {
        if (null === $this->metaData) {
            $entityFqn      = $this->getEntityFqn();
            $this->metaData = $this->entityManager->getClassMetadata($entityFqn);
        }

        $this->entityRepository = new EntityRepository($this->entityManager, $this->metaData);
    }

    protected function getEntityFqn(): string
    {
        return '\\' . str_replace(
            [
                    'Entity\\Repositories',
                ],
            [
                    'Entities',
                ],
            $this->namespaceHelper->cropSuffix(static::class, 'Repository')
        );
    }

    public function getRandomResultFromQueryBuilder(QueryBuilder $queryBuilder, string $entityAlias): ?EntityInterface
    {
        $count = $this->getCountForQueryBuilder($queryBuilder, $entityAlias);
        if (0 === $count) {
            return null;
        }

        $queryBuilder->setMaxResults(1);
        $limitIndex = random_int(0, $count - 1);
        $results    = $queryBuilder->getQuery()
                                   ->setFirstResult($limitIndex)
                                   ->execute();
        $entity     = current($results);
        if (null === $entity) {
            return null;
        }
        $this->initialiseEntity($entity);

        return $entity;
    }

    public function getCountForQueryBuilder(QueryBuilder $queryBuilder, string $aliasToCount): int
    {
        $clone = clone $queryBuilder;
        $clone->select($queryBuilder->expr()->count($aliasToCount));

        return (int)$clone->getQuery()->getSingleScalarResult();
    }

    public function initialiseEntity(EntityInterface $entity)
    {
        $this->entityFactory->initialiseEntity($entity);

        return $entity;
    }

    /**
     * @return array|EntityInterface[]
     */
    public function findAll(): array
    {
        return $this->initialiseEntities($this->entityRepository->findAll());
    }

    public function initialiseEntities($entities)
    {
        foreach ($entities as $entity) {
            $this->initialiseEntity($entity);
        }

        return $entities;
    }

    /**
     * @param mixed    $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     *
     * @return EntityInterface
     * @throws DoctrineStaticMetaException
     */
    public function get($id, ?int $lockMode = null, ?int $lockVersion = null)
    {
        try {
            $entity = $this->find($id, $lockMode, $lockVersion);
        } catch (ConversionException $e) {
            $error = 'Failed getting by id ' . $id
                     . ', unless configured as an int ID entity, this should be a valid UUID';
            throw new DoctrineStaticMetaException($error, $e->getCode(), $e);
        }
        if ($entity === null) {
            throw new DoctrineStaticMetaException('Could not find the entity with id ' . $id);
        }

        return $this->initialiseEntity($entity);
    }

    /**
     * @param mixed    $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     *
     * @return EntityInterface|null
     */
    public function find($id, ?int $lockMode = null, ?int $lockVersion = null)
    {
        $entity = $this->entityRepository->find($id, $lockMode, $lockVersion);
        if (null === $entity) {
            return null;
        }
        if ($entity instanceof EntityInterface) {
            $this->initialiseEntity($entity);

            return $entity;
        }
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return EntityInterface
     */
    public function getOneBy(array $criteria, ?array $orderBy = null)
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if ($result === null) {
            throw new RuntimeException('Could not find the entity');
        }

        return $this->initialiseEntity($result);
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return EntityInterface|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        $criteria = $this->mapCriteriaSetUuidsToStrings($criteria);
        $entity   = $this->entityRepository->findOneBy($criteria, $orderBy);
        if (null === $entity) {
            return null;
        }
        if ($entity instanceof EntityInterface) {
            $this->initialiseEntity($entity);

            return $entity;
        }
    }

    public function mapCriteriaSetUuidsToStrings(array $criteria): array
    {
        foreach ($criteria as $property => $value) {
            if ($value instanceof EntityInterface) {
                $criteria[$property] = $value->getId();
            }
            if ($value instanceof UuidInterface) {
                $criteria[$property] = $value->toString();
            }
        }

        return $criteria;
    }

    /**
     * @param array $criteria
     *
     * @return EntityInterface|null
     */
    public function getRandomOneBy(array $criteria)
    {
        $found = $this->getRandomBy($criteria);
        if ([] === $found) {
            throw new RuntimeException('Failed finding any Entities with this criteria');
        }
        $entity = current($found);
        if ($entity instanceof EntityInterface) {
            return $entity;
        }
        throw new RuntimeException('Unexpected Entity Type ' . get_class($entity));
    }

    /**
     * @param array $criteria
     *
     * @param int   $numToGet
     *
     * @return EntityInterface[]|array
     */
    public function getRandomBy(array $criteria, int $numToGet = 1): array
    {
        $count = $this->count($criteria);
        if (0 === $count) {
            return [];
        }
        $randOffset = rand(0, $count - $numToGet);

        return $this->findBy($criteria, null, $numToGet, $randOffset);
    }

    public function count(array $criteria = []): int
    {
        $criteria = $this->mapCriteriaSetUuidsToStrings($criteria);

        return $this->entityRepository->count($criteria);
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array|EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $criteria = $this->mapCriteriaSetUuidsToStrings($criteria);

        return $this->initialiseEntities($this->entityRepository->findBy($criteria, $orderBy, $limit, $offset));
    }

    public function matching(Criteria $criteria): LazyCriteriaCollection
    {
        $collection = $this->entityRepository->matching($criteria);
        if ($collection instanceof LazyCriteriaCollection) {
            return $this->initialiseEntities($collection);
        }
    }

    public function createResultSetMappingBuilder(string $alias): Query\ResultSetMappingBuilder
    {
        return $this->entityRepository->createResultSetMappingBuilder($alias);
    }

    public function createNamedQuery(string $queryName): Query
    {
        return $this->entityRepository->createNamedQuery($queryName);
    }

    public function createNativeNamedQuery(string $queryName): NativeQuery
    {
        return $this->entityRepository->createNativeNamedQuery($queryName);
    }

    public function clear(): void
    {
        throw new \ErrorException(
            'Calling clear on entity repositories is deprecated as it tries to clear only specific entities ' .
            'which is not very well supported.' .
            ' If you want to clear the full unit of work, then use the EntityManager directly.',
            E_USER_DEPRECATED
        );
    }

    /**
     * Create a query builder with the alias preset
     *
     * @param string|null $indexBy
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderWithAlias(string $indexBy = null): QueryBuilder
    {
        return $this->createQueryBuilder($this->getAlias(), $indexBy);
    }

    public function createQueryBuilder(string $alias, string $indexBy = null): QueryBuilder
    {
        return (new UuidQueryBuilder($this->entityManager))
            ->select($alias)
            ->from($this->getClassName(), $alias, $indexBy);
    }

    public function getClassName(): string
    {
        return $this->entityRepository->getClassName();
    }

    /**
     * Generate an alias based on the class name
     *
     * removes the words entity and repository
     *
     * gets all the upper case letters and returns them as a lower case string
     *
     * Warning - nothing is done to guarantee uniqueness for now
     *
     * @return string
     */
    public function getAlias(): string
    {
        if (isset(static::$aliasCache[static::class])) {
            return static::$aliasCache[static::class];
        }
        $class           = $this->namespaceHelper->getClassShortName($this->getClassName());
        $removeStopWords = str_ireplace(['entity', 'repository'], '', $class);
        $ucOnly          = preg_replace('%[^A-Z]%', '', $removeStopWords);

        static::$aliasCache[static::class] = strtolower($ucOnly);

        return static::$aliasCache[static::class];
    }

    public function createDeletionQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createDeletionQueryBuilder($this->getAlias());
    }

    public function createDeletionQueryBuilder(string $alias): QueryBuilder
    {
        return (new UuidQueryBuilder($this->entityManager))
            ->delete($this->getClassName(), $alias);
    }

    /**
     * For use with query builder, auto prefix alias
     *
     * @param string $property
     *
     * @return string
     */
    public function aliasPrefix(string $property): string
    {
        return $this->getAlias() . '.' . $property;
    }
}
