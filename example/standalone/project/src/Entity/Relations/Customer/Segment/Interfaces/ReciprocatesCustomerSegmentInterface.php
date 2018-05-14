<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Interfaces;

use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

interface ReciprocatesCustomerSegmentInterface
{
    /**
     * @param CustomerSegment $customerSegment
     *
     * @return self
     */
    public function reciprocateRelationOnCustomerSegment(
        CustomerSegment $customerSegment
    ): self;

    /**
     * @param CustomerSegment $customerSegment
     *
     * @return self
     */
    public function removeRelationOnCustomerSegment(
        CustomerSegment $customerSegment
    ): self;
}
