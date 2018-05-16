<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class AbstractEntityRepository
 *
 * This provides a base class that handles instantiating the correctly configured EntityRepository and provides an
 * extensible baseline for further customisation
 *
 * We have extracted an interface from the standard Doctrine EntityRepository and implement that
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
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
     * AbstractEntityRepositoryFactory constructor.
     *
     * @param EntityManager      $entityManager
     * @param ClassMetadata|null $metaData
     */
    public function __construct(EntityManager $entityManager, ?ClassMetadata $metaData = null)
    {
        $this->entityManager = $entityManager;
        $this->metaData      = $metaData;
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
        return '\\'.\str_replace(
                [
                    'Entity\\Repositories',
                    'Repository',
                ],
                [
                    'Entities',
                    '',
                ],
                static::class
            );
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->entityRepository->find($id);
    }

    public function findAll()
    {
        return $this->entityRepository->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->entityRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->entityRepository->findOneBy($criteria, $orderBy);
    }

    public function getClassName()
    {
        return $this->entityRepository->getClassName();
    }

    public function matching(Criteria $criteria)
    {
        return $this->entityRepository->matching($criteria);
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        return $this->entityRepository->createQueryBuilder($alias, $indexBy);
    }

    public function createResultSetMappingBuilder($alias)
    {
        return $this->entityRepository->createResultSetMappingBuilder($alias);
    }

    public function createNamedQuery($queryName)
    {
        return $this->entityRepository->createNamedQuery($queryName);
    }

    public function createNativeNamedQuery($queryName)
    {
        return $this->entityRepository->createNativeNamedQuery($queryName);
    }

    public function clear()
    {
        $this->entityRepository->clear();
    }

    public function count(array $criteria)
    {
        return $this->entityRepository->count($criteria);
    }
}
