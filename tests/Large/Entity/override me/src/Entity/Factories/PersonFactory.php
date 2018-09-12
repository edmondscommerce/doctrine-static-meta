<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Person;
use My\Test\Project\Entity\Interfaces\PersonInterface;
// phpcs: enable
class PersonFactory extends AbstractEntityFactory
{
    public function create(array $values = []): PersonInterface
    {
        return $this->entityFactory->create(Person::class, $values);
    }
}
