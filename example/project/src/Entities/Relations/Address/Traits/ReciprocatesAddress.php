<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Address\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Address;

trait ReciprocatesAddress
{
    /**
     * This method needs to set the relationship on the address to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Address $address
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnAddress(Address $address): UsesPHPMetaDataInterface
    {
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
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnAddress(Address $address): UsesPHPMetaDataInterface
    {
        $method = 'remove'.static::getSingular();
        $address->$method($this, false);

        return $this;
    }

}
