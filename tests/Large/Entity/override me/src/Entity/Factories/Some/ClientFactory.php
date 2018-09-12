<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Some;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Some\Client;
use My\Test\Project\Entity\Interfaces\Some\ClientInterface;
// phpcs: enable
class ClientFactory extends AbstractEntityFactory
{
    public function create(array $values = []): ClientInterface
    {
        return $this->entityFactory->create(Client::class, $values);
    }
}
