<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

trait ReciprocatesCustomerSegment
{
    /**
     * This method needs to set the relationship on the customerSegment to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param CustomerSegment $customerSegment
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnCustomerSegment(
        CustomerSegment $customerSegment
    ): UsesPHPMetaDataInterface {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($customerSegment, $method)) {
            $method = 'set'.$singular;
        }

        $customerSegment->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the customerSegment to this entity.
     *
     * @param CustomerSegment $customerSegment
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnCustomerSegment(
        CustomerSegment $customerSegment
    ): UsesPHPMetaDataInterface {
        $method = 'remove'.static::getSingular();
        $customerSegment->$method($this, false);

        return $this;
    }

}
