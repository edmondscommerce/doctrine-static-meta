<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\EntityRepositoryInterface;
use RuntimeException;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Repositories\AbstractEntityRepository as ProjectAbstractEntityRepository;
use function get_class;

/**
 * @implements EntityRepositoryInterface<TemplateEntityInterface>
 * @extends    ProjectAbstractEntityRepository<TemplateEntityInterface>
 */
class TemplateEntityRepository extends ProjectAbstractEntityRepository implements EntityRepositoryInterface
{
    public function find(mixed $id, ?int $lockMode = null, ?int $lockVersion = null): ?TemplateEntityInterface
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    public function get(mixed $id, ?int $lockMode = null, ?int $lockVersion = null): TemplateEntityInterface
    {
        return parent::get($id, $lockMode, $lockVersion);
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
        return parent::findOneBy($criteria, $orderBy);
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
        return parent::getRandomOneBy($criteria);
    }

    /**
     * @param array<string,mixed> $criteria
     * @param int                 $numToGet
     *
     * @return TemplateEntityInterface[]
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
