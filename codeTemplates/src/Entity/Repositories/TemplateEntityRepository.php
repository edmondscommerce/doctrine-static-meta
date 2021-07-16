<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use RuntimeException;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Repositories\AbstractEntityRepository as ProjectAbstractEntityRepository;
use function get_class;

class TemplateEntityRepository extends ProjectAbstractEntityRepository
{
    public function find(mixed $id, ?int $lockMode = null, ?int $lockVersion = null): ?TemplateEntityInterface
    {
        $result = parent::find($id, $lockMode, $lockVersion);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }

        throw new RuntimeException('Unknown entity type of ' . get_class($result) . ' returned');
    }

    public function get(mixed $id, ?int $lockMode = null, ?int $lockVersion = null): TemplateEntityInterface
    {
        $result = parent::get($id, $lockMode, $lockVersion);
        if ($result instanceof TemplateEntityInterface) {
            return $result;
        }
        throw new RuntimeException('Unknown entity type of ' . get_class($result) . ' returned');
    }

    /**
     * @param array<string,mixed>        $criteria
     * @param array<string, string>|null $orderBy
     */
    public function getOneBy(array $criteria, ?array $orderBy = null): TemplateEntityInterface
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if ($result === null) {
            throw new RuntimeException('Could not find the entity');
        }

        return $result;
    }

    /**
     * @param array<string,mixed>        $criteria
     * @param array<string, string>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?TemplateEntityInterface
    {
        $result = parent::findOneBy($criteria, $orderBy);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }

        throw new RuntimeException('Unknown entity type of ' . get_class($result) . ' returned');
    }

    /**
     * @param array<string,mixed>        $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return TemplateEntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return TemplateEntityInterface[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    /**
     * @param array<string,mixed> $criteria
     */
    public function getRandomOneBy(array $criteria): TemplateEntityInterface
    {
        $result = parent::getRandomOneBy($criteria);
        if ($result instanceof TemplateEntityInterface) {
            return $result;
        }
        throw new RuntimeException('Unknown entity type of ' . get_class($result) . ' returned');
    }

    /**
     * @param array<string,mixed> $criteria
     * @param int                 $numToGet
     *
     * @return TemplateEntityInterface[]|array|EntityInterface[]
     */
    public function getRandomBy(array $criteria, int $numToGet = 1): array
    {
        return parent::getRandomBy($criteria, $numToGet);
    }

    public function initialiseEntity(EntityInterface $entity): TemplateEntityInterface
    {
        if (!($entity instanceof TemplateEntityInterface)) {
            throw new \InvalidArgumentException(
                '$entity is ' . $entity::class . ', must be an instance of ' . TemplateEntityInterface::class
            );
        }
        parent::initialiseEntity($entity);

        return $entity;
    }


}
