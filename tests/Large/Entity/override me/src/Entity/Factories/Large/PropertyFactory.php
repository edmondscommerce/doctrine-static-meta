<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Large;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Large\Property;
use My\Test\Project\Entity\Interfaces\Large\PropertyInterface;
// phpcs: enable
class PropertyFactory extends AbstractEntityFactory
{
    public function create(array $values = []): PropertyInterface
    {
        return $this->entityFactory->create(Property::class, $values);
    }
}
