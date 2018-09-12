<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories\Another\Deeply\Nested;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Another\Deeply\Nested\Client;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;
// phpcs: enable
class ClientFactory extends AbstractEntityFactory
{
    public function create(array $values = []): ClientInterface
    {
        return $this->entityFactory->create(Client::class, $values);
    }
}
