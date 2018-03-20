<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

interface ReciprocatesOrderLineItemInterface
{
    /**
     * @param OrderLineItem $orderLineItem
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnOrderLineItem(
        OrderLineItem $orderLineItem
    ): UsesPHPMetaDataInterface;

    /**
     * @param OrderLineItem $orderLineItem
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnOrderLineItem(
        OrderLineItem $orderLineItem
    ): UsesPHPMetaDataInterface;
}
