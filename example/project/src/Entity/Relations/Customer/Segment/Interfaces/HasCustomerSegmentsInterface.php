<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

interface HasCustomerSegmentsInterface
{
    public const PROPERTY_NAME_CUSTOMER_SEGMENTS = 'customerSegments';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForCustomerSegments(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|CustomerSegment[]
     */
    public function getCustomerSegments(): Collection;

    /**
     * @param Collection|CustomerSegment[] $customerSegments
     *
     * @return self
     */
    public function setCustomerSegments(Collection $customerSegments);

    /**
     * @param CustomerSegment $customerSegment
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCustomerSegment(
        CustomerSegment $customerSegment,
        bool $recip = true
    );

    /**
     * @param CustomerSegment $customerSegment
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCustomerSegment(
        CustomerSegment $customerSegment,
        bool $recip = true
    );

}
