<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits;

use My\Test\Project\Entities\Customer\Segment as CustomerSegment;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\ReciprocatesCustomerSegmentInterface;

trait ReciprocatesCustomerSegment
{
    /**
     * This method needs to set the relationship on the customerSegment to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param CustomerSegment|null $customerSegment
     *
     * @return ReciprocatesCustomerSegmentInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnCustomerSegment(
        CustomerSegment $customerSegment
    ): ReciprocatesCustomerSegmentInterface {
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
     * @return ReciprocatesCustomerSegmentInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnCustomerSegment(
        CustomerSegment $customerSegment
    ): ReciprocatesCustomerSegmentInterface {
        $method = 'remove'.static::getSingular();
        $customerSegment->$method($this, false);

        return $this;
    }
}
