<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Interfaces;

use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

interface ReciprocatesOrderLineItemInterface
{
    /**
     * @param OrderLineItem $orderLineItem
     *
     * @return self
     */
    public function reciprocateRelationOnOrderLineItem(
        OrderLineItem $orderLineItem
    ): self;

    /**
     * @param OrderLineItem $orderLineItem
     *
     * @return self
     */
    public function removeRelationOnOrderLineItem(
        OrderLineItem $orderLineItem
    ): self;
}
