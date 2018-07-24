<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;

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
     * @var EntityManager
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
     * @var EntityValidatorFactory
     */
    private static $entityValidatorFactory;


    /**
     * AbstractEntityRepositoryFactory constructor.
     *
     * @param EntityManager          $entityManager
     * @param EntityValidatorFactory $entityValidatorFactory
     * @param ClassMetadata|null     $metaData
     * @param NamespaceHelper|null   $namespaceHelper
     */
    public function __construct(
        EntityManager $entityManager,
        ?ClassMetadata $metaData = null,
        ?NamespaceHelper $namespaceHelper = null
    ) {
        $this->entityManager   = $entityManager;
        $this->metaData        = $metaData;
        $this->namespaceHelper = ($namespaceHelper ?? new NamespaceHelper());
        $this->initRepository();
    }

    private static function getEntityValidatorFactory(self $that): EntityValidatorFactory
    {
        if (null === self::$entityValidatorFactory) {
            /**
             * Can't use DI because Doctrine uses it's own factory method for repositories
             */
            self::$entityValidatorFactory = new EntityValidatorFactory(
                new DoctrineCache(
                    $that->entityManager->getCache() ?? new ArrayCache()
                )
            );
        }

        return self::$entityValidatorFactory;
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
        return '\\'.\str_replace(
                [
                    'Entity\\Repositories',
                ],
                [
                    'Entities',
                ],
                $this->namespaceHelper->cropSuffix(static::class, 'Repository')
            );
    }

    public function find($id, ?int $lockMode = null, ?int $lockVersion = null): ?EntityInterface
    {
        $entity = $this->entityRepository->find($id, $lockMode, $lockVersion);
        if (null === $entity || $entity instanceof EntityInterface) {
            return $this->injectValidatorIfNotNull($entity);
        }
        throw new \TypeError('Returned result is neither null nor an instance of EntityInterface');
    }

    private function injectValidatorIfNotNull(?EntityInterface $entity): ?EntityInterface
    {
        if (null !== $entity) {
            $entity->injectValidator(self::getEntityValidatorFactory($this)->getEntityValidator());
        }

        return $entity;
    }

    private function injectValidatorToCollection(iterable $collection)
    {
        foreach ($collection as $entity) {
            $this->injectValidatorIfNotNull($entity);
        }

        return $collection;
    }

    /**
     * @return array|EntityInterface[]
     */
    public function findAll(): array
    {
        $collection = $this->entityRepository->findAll();

        return $this->injectValidatorToCollection($collection);
    }

    /**
     * @return array|EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $entity = $this->entityRepository->findBy($criteria, $orderBy, $limit, $offset);
        if (null === $entity || $entity instanceof EntityInterface) {
            return $this->injectValidatorIfNotNull($entity);
        }
        throw new \TypeError('Returned result is neither null nor an instance of EntityInterface');
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?EntityInterface
    {
        $entity = $this->entityRepository->findOneBy($criteria, $orderBy);
        if (null === $entity || $entity instanceof EntityInterface) {
            return $this->injectValidatorIfNotNull($entity);
        }
        throw new \TypeError('Returned result is neither null nor an instance of EntityInterface');
    }

    public function getClassName(): string
    {
        return $this->entityRepository->getClassName();
    }

    public function matching(Criteria $criteria): LazyCriteriaCollection
    {
        $collection = $this->entityRepository->matching($criteria);
        if ($collection instanceof LazyCriteriaCollection) {
            return $this->injectValidatorToCollection($collection);
        }
        throw new \TypeError('Returned result is not an instance of LazyCriteriaCollection');
    }

    public function createQueryBuilder(string $alias, string $indexBy = null): QueryBuilder
    {
        return $this->entityRepository->createQueryBuilder($alias, $indexBy);
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

    /**
     *
     */
    public function clear(): void
    {
        $this->entityRepository->clear();
    }

    public function count(array $criteria): int
    {
        return $this->entityRepository->count($criteria);
    }
}
