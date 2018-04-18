<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegment;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Segment\Traits\ReciprocatesCustomerSegment;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;
use  My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegmentAbstract;

/**
 * Trait HasCustomerSegmentManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of CustomerSegment.
 *
 * CustomerSegment has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\CustomerSegment\HasCustomerSegment
 */
trait HasCustomerSegmentManyToOne
{
    use HasCustomerSegmentAbstract;

    use ReciprocatesCustomerSegment;

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
            CustomerSegment::class,
            static::getPlural()
        );
    }
}
