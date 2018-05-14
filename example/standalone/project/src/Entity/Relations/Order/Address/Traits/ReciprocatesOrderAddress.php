<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Traits;

use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;

trait ReciprocatesOrderAddress
{
    /**
     * This method needs to set the relationship on the orderAddress to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param OrderAddress|null $orderAddress
     *
     * @return ReciprocatesOrderAddressInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnOrderAddress(
        OrderAddress $orderAddress
    ): ReciprocatesOrderAddressInterface {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($orderAddress, $method)) {
            $method = 'set'.$singular;
        }

        $orderAddress->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the orderAddress to this entity.
     *
     * @param OrderAddress $orderAddress
     *
     * @return ReciprocatesOrderAddressInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnOrderAddress(
        OrderAddress $orderAddress
    ): ReciprocatesOrderAddressInterface {
        $method = 'remove'.static::getSingular();
        $orderAddress->$method($this, false);

        return $this;
    }
}
