<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

interface HasCustomerSegmentInterface
{
    public const PROPERTY_NAME_CUSTOMER_SEGMENT = 'customerSegment';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForCustomerSegment(ClassMetadataBuilder $builder): void;

    /**
     * @return null|CustomerSegment
     */
    public function getCustomerSegment(): ?CustomerSegment;

    /**
     * @param CustomerSegment|null $customerSegment
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCustomerSegment(
        ?CustomerSegment $customerSegment,
        bool $recip = true
    ): HasCustomerSegmentInterface;

    /**
     * @return self
     */
    public function removeCustomerSegment(): HasCustomerSegmentInterface;
}
