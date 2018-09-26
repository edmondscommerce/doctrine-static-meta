<?php declare(strict_types=1);

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
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

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
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * AbstractEntityRepositoryFactory constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ClassMetadata|null     $metaData
     * @param NamespaceHelper|null   $namespaceHelper
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityFactory $entityFactory,
        ?ClassMetadata $metaData = null,
        ?NamespaceHelper $namespaceHelper = null
    ) {
        $this->entityManager   = $entityManager;
        $this->metaData        = $metaData;
        $this->namespaceHelper = ($namespaceHelper ?? new NamespaceHelper());
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
        return '\\' . \str_replace(
                [
                    'Entity\\Repositories',
                ],
                [
                    'Entities',
                ],
                $this->namespaceHelper->cropSuffix(static::class, 'Repository')
            );
    }

    /**
     * @return array|EntityInterface[]
     */
    public function findAll(): array
    {
        return $this->initiliseEntities($this->entityRepository->findAll());
    }

    private function initiliseEntities(array $entities)
    {
        foreach ($entities as $entity) {
            $this->$this->initialiseEntity($entity);
        }
    }

    /**
     * @return array|EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->initiliseEntities($this->entityRepository->findBy($criteria, $orderBy, $limit, $offset));
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

    private function initialiseEntity(EntityInterface $entity)
    {
        $this->entityFactory->initialiseEntity($entity);
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
            throw new \RuntimeException('Could not find the entity');
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
        $entity = $this->entityRepository->findOneBy($criteria, $orderBy);
        if (null === $entity) {
            return null;
        }
        if ($entity instanceof EntityInterface) {
            $this->initialiseEntity($entity);

            return $entity;
        }
    }

    public function getClassName(): string
    {
        return $this->entityRepository->getClassName();
    }

    public function matching(Criteria $criteria): LazyCriteriaCollection
    {
        $collection = $this->entityRepository->matching($criteria);
        if ($collection instanceof LazyCriteriaCollection) {
            return $this->initiliseEntities($collection);
        }
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

    public function clear(): void
    {
        $this->entityRepository->clear();
    }

    public function count(array $criteria = []): int
    {
        return $this->entityRepository->count($criteria);
    }
}
