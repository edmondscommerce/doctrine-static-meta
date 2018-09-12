<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Repositories\Large;

use My\Test\Project\Entity\Repositories\AbstractEntityRepository;
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;

// phpcs:disable -- line length
class RelationRepository extends AbstractEntityRepository
{
// phpcs: enable

    public function find($id, ?int $lockMode = null, ?int $lockVersion = null): ?RelationInterface
    {
        $result = parent::find($id, $lockMode, $lockVersion);
        if ($result === null || $result instanceof RelationInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function get($id, ?int $lockMode = null, ?int $lockVersion = null): RelationInterface
    {
        $result = parent::get($id, $lockMode, $lockVersion);
        if ($result instanceof RelationInterface) {
            return $result;
        }
        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }

    public function getOneBy(array $criteria, ?array $orderBy = null): RelationInterface
    {
        $result = $this->findOneBy($criteria, $orderBy);
        if ($result === null) {
            throw new \RuntimeException('Could not find the entity');
        }

        return $result;
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?RelationInterface
    {
        $result = parent::findOneBy($criteria, $orderBy);
        if ($result === null || $result instanceof RelationInterface) {
            return $result;
        }

        throw new \RuntimeException('Unknown entity type of ' . \get_class($result) . ' returned');
    }
}
