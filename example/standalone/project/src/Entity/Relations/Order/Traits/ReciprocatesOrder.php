<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Traits;

use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;

trait ReciprocatesOrder
{
    /**
     * This method needs to set the relationship on the order to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Order|null $order
     *
     * @return ReciprocatesOrderInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnOrder(
        Order $order
    ): ReciprocatesOrderInterface {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($order, $method)) {
            $method = 'set'.$singular;
        }

        $order->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the order to this entity.
     *
     * @param Order $order
     *
     * @return ReciprocatesOrderInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnOrder(
        Order $order
    ): ReciprocatesOrderInterface {
        $method = 'remove'.static::getSingular();
        $order->$method($this, false);

        return $this;
    }
}
