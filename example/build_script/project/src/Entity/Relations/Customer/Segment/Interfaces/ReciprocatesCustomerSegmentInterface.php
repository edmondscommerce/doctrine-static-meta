<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

interface ReciprocatesCustomerSegmentInterface
{
    /**
     * @param CustomerSegment $customerSegment
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnCustomerSegment(
        CustomerSegment $customerSegment
    ): UsesPHPMetaDataInterface;

    /**
     * @param CustomerSegment $customerSegment
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnCustomerSegment(
        CustomerSegment $customerSegment
    ): UsesPHPMetaDataInterface;
}
