<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Large;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Large\Data;
use My\Test\Project\Entity\Interfaces\Large\DataInterface;
// phpcs: enable
class DataFactory extends AbstractEntityFactory
{
    public function create(array $values = []): DataInterface
    {
        return $this->entityFactory->create(Data::class, $values);
    }
}
