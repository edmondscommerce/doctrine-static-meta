<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Factories;
// phpcs:disable -- line length
use My\Test\Project\Entity\Factories\AbstractEntityFactory;
use My\Test\Project\Entities\Order;
use My\Test\Project\Entity\Interfaces\OrderInterface;
// phpcs: enable
class OrderFactory extends AbstractEntityFactory
{
    public function create(array $values = []): OrderInterface
    {
        return $this->entityFactory->create(Order::class, $values);
    }
}
