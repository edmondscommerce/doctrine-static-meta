<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Attributes;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Attributes\Address;
use My\Test\Project\Entity\Interfaces\Attributes\AddressInterface;
// phpcs: enable
class AddressFactory extends AbstractEntityFactory
{
    public function create(array $values = []): AddressInterface
    {
        return $this->entityFactory->create(Address::class, $values);
    }
}
