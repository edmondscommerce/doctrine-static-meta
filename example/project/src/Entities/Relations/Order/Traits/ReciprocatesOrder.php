<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order;

trait ReciprocatesOrder
{
    /**
     * This method needs to set the relationship on the order to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Order $order
     *
     * @return $this||UsesPHPMetaData
     */
    public function reciprocateRelationOnOrder(Order $order): UsesPHPMetaDataInterface
    {
        $singular = static::getSingular();
        $method   = 'add' . $singular;
        if (!method_exists($order, $method)) {
            $method = 'set' . $singular;
        }

        $order->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the order to this entity.
     *
     * @param Order $order
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeRelationOnOrder(Order $order): UsesPHPMetaDataInterface
    {
        $method = 'remove' . static::getSingular();
        $order->$method($this, false);

        return $this;
    }

}
