<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegment;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegmentAbstract;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

/**
 * Trait HasCustomerSegmentManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\CustomerSegment\HasCustomerSegment
 */
trait HasCustomerSegmentUnidirectionalManyToOne
{
    use HasCustomerSegmentAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomerSegment(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            CustomerSegment::getSingular(),
            CustomerSegment::class
        );
    }
}
