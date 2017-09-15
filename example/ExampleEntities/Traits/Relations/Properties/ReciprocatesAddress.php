<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\ExampleEntities\Traits\Relations\Properties;

use EdmondsCommerce\DoctrineStaticMeta\Properties\Address;

trait ReciprocatesAddress
{
    /**
     * This method needs to set the relationship on the address to this entity
     * @param Address $address
     * @return $this
     */
    protected function reciprocateRelationOnAddress(Address $address)
    {
        $method = 'add'.static::getSingular();
        $address->$method($this, false);
    }

    /**
     * This method needs to remove the relationship on the address to this entity
     * @param Address $address
     * @return $this
     */
    protected function removeRelationOnAddress(Address $address)
    {
        $method = 'remove'.static::getSingular();
        $address->$method($this, false);
    }
}
