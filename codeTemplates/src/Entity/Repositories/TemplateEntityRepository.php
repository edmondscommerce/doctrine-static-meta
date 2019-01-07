<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Repositories\AbstractEntityRepository as ProjectAbstractEntityRepository;


class TemplateEntityRepository extends ProjectAbstractEntityRepository
{
    public function find($id, ?int $lockMode = null, ?int $lockVersion = null): ?TemplateEntityInterface
    {
        $result = parent::find($id, $lockMode, $lockVersion);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function get($id, ?int $lockMode = null, ?int $lockVersion = null): TemplateEntityInterface
    {
        $result = parent::get($id, $lockMode, $lockVersion);
        if ($result instanceof TemplateEntityInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function getOneBy(array $criteria, ?array $orderBy = null): TemplateEntityInterface
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if ($result === null) {
            throw new \RuntimeException('Could not find the entity');
        }

        return $result;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?TemplateEntityInterface
    {
        $result = parent::findOneBy($criteria, $orderBy);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return TemplateEntityInterface[]|array|EntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return TemplateEntityInterface[]|array|EntityInterface[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }




    public function getRandomOneBy(array $criteria): ?TemplateEntityInterface
    {
        $result = parent::getRandomOneBy($criteria);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    /**
     * @param array $criteria
     * @param int   $numToGet
     *
     * @return TemplateEntityInterface[]|array|EntityInterface[]
     */
    public function getRandomBy(array $criteria, int $numToGet = 1): array
    {
        return parent::getRandomBy($criteria, $numToGet);
    }

    /**
     * @param EntityInterface|TemplateEntityInterface $entity
     *
     * @return TemplateEntityInterface
     */
    public function initialiseEntity(EntityInterface $entity): TemplateEntityInterface
    {
        return parent::initialiseEntity($entity);
    }


}
