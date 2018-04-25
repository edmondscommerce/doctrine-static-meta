<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

trait ReciprocatesOrderLineItem
{
    /**
     * This method needs to set the relationship on the orderLineItem to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param OrderLineItem $orderLineItem
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnOrderLineItem(
        OrderLineItem $orderLineItem
    ): UsesPHPMetaDataInterface {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($orderLineItem, $method)) {
            $method = 'set'.$singular;
        }

        $orderLineItem->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the orderLineItem to this entity.
     *
     * @param OrderLineItem $orderLineItem
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnOrderLineItem(
        OrderLineItem $orderLineItem
    ): UsesPHPMetaDataInterface {
        $method = 'remove'.static::getSingular();
        $orderLineItem->$method($this, false);

        return $this;
    }

}
