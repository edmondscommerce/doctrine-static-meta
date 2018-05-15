<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class AbstractEntityRepository
 *
 * This provides a base class that handles instantiating the correctly configured EntityRepository and provides an
 * extensible baseline for further customisation
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories
 */
abstract class AbstractEntityRepository implements ObjectRepository, Selectable
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
     * @var ClassMetadataInfo|null
     */
    protected $metaData;

    /**
     * AbstractEntityRepositoryFactory constructor.
     *
     * @param EntityManager          $entityManager
     * @param ClassMetadataInfo|null $metaData
     */
    public function __construct(EntityManager $entityManager, ?ClassMetadataInfo $metaData)
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

    protected function getEntityFqn()
    {
        return '\\'.\str_replace(
                ['Entity\\Repositories', 'Repository'],
                ['Entities', ''],
                static::class
            );
    }

    public function find($id)
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

    public function findOneBy(array $criteria)
    {
        return $this->entityRepository->findOneBy($criteria);
    }

    public function getClassName()
    {
        return $this->entityRepository->getClassName();
    }

    public function matching(Criteria $criteria)
    {
        return $this->entityRepository->matching($criteria);
    }


}
