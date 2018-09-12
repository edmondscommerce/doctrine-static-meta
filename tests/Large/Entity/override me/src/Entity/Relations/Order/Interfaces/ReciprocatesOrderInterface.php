<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Interfaces;

use My\Test\Project\Entity\Interfaces\OrderInterface;

interface ReciprocatesOrderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return self
     */
    public function reciprocateRelationOnOrder(
        OrderInterface $order
    ): self;

    /**
     * @param OrderInterface $order
     *
     * @return self
     */
    public function removeRelationOnOrder(
        OrderInterface $order
    ): self;
}
