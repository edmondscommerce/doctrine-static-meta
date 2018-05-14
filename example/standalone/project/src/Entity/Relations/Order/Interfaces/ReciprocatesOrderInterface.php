<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Interfaces;

use My\Test\Project\Entities\Order as Order;

interface ReciprocatesOrderInterface
{
    /**
     * @param Order $order
     *
     * @return self
     */
    public function reciprocateRelationOnOrder(
        Order $order
    ): self;

    /**
     * @param Order $order
     *
     * @return self
     */
    public function removeRelationOnOrder(
        Order $order
    ): self;
}
