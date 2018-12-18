<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\LazyCriteriaCollection;
use Doctrine\ORM\Mapping;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityFactoryInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;

/**
 * Class DoctrineEntityRepository
 *
 * Overrides the default Doctrine repository, allows for injection of required DSM classes at a single point of entry
 */
class DoctrineEntityRepository extends EntityRepository
{
    /**
     * @var EntityFactoryInterface
     */
    private $entityFactory;

    public function __construct($em, Mapping\ClassMetadata $class, EntityFactoryInterface $entityFactory)
    {
        parent::__construct($em, $class);

        $this->entityFactory = $entityFactory;
    }

    /**
     * @param object|null $entity
     *
     * @return EntityInterface|null
     */
    private function initialiseEntity(?object $entity): ?EntityInterface
    {
        if (!($entity instanceof EntityInterface)) {
            return null;
        }

        $this->entityFactory->initialiseEntity($entity);

        return $entity;
    }

    private function initialiseEntities($entities): array
    {
        foreach ($entities as $entity) {
            $this->initialiseEntity($entity);
        }

        return $entities;
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return $this->initialiseEntity(parent::find($id, $lockMode, $lockVersion));
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->initialiseEntities(parent::findBy($criteria, $orderBy, $limit, $offset));
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->initialiseEntity(parent::findOneBy($criteria, $orderBy));
    }

    public function matching(Criteria $criteria)
    {
        $collection = parent::matching($criteria);
        if ($collection instanceof LazyCriteriaCollection) {
            return $this->initialiseEntities($collection);
        }

        return $collection;
    }


}
