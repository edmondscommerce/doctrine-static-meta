<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Repositories;

use FQNFor\AbstractEntityRepository;
use TemplateNamespace\Entity\Interfaces;

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

        throw new \RuntimeException('Unknown entity type of ' . get_class($result) . ' returned');
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?TemplateEntityInterface
    {
        $result = parent::findOneBy($criteria, $orderBy);
        if ($result === null || $result instanceof TemplateEntityInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . get_class($result) . ' returned');
    }

    public function get($id, ?int $lockMode = null, ?int $lockVersion = null): TemplateEntityInterface
    {
        $result = $this->find($id, $lockMode, $lockVersion);
        if ($result === null) {
            throw new \RuntimeException('Could not find the entity');
        }

        return $result;
    }

    public function getOneBy(array $criteria, ?array $orderBy = null): TemplateEntityInterface
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if ($result === null) {
            throw new \RuntimeException('Could not find the entity');
        }

        return $result;
    }
}
