<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Traits;

use My\Test\Project\Entities\Address as Address;
use My\Test\Project\Entity\Relations\Address\Interfaces\ReciprocatesAddressInterface;

trait ReciprocatesAddress
{
    /**
     * This method needs to set the relationship on the address to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Address|null $address
     *
     * @return ReciprocatesAddressInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnAddress(
        Address $address
    ): ReciprocatesAddressInterface {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($address, $method)) {
            $method = 'set'.$singular;
        }

        $address->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the address to this entity.
     *
     * @param Address $address
     *
     * @return ReciprocatesAddressInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnAddress(
        Address $address
    ): ReciprocatesAddressInterface {
        $method = 'remove'.static::getSingular();
        $address->$method($this, false);

        return $this;
    }
}
