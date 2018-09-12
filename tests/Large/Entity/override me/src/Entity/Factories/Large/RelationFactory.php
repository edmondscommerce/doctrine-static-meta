<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Large;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Large\Relation;
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;
// phpcs: enable
class RelationFactory extends AbstractEntityFactory
{
    public function create(array $values = []): RelationInterface
    {
        return $this->entityFactory->create(Relation::class, $values);
    }
}
