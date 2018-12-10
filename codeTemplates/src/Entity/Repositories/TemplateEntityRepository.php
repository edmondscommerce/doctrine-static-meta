<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;

use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

// phpcs:disable -- line length
class TemplateEntityRepository extends AbstractEntityRepository
{
// phpcs: enable

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
     * @return array|TemplateEntityInterface[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    public function getRandomBy(array $criteria): ?TemplateEntityInterface
    {
        $result = parent::getRandomBy($criteria);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');

    }


}
