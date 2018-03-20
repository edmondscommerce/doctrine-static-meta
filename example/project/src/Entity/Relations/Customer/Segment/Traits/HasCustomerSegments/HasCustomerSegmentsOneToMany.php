<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegments;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegmentsAbstract;
use  My\Test\Project\Entity\Relations\Customer\Segment\Traits\ReciprocatesCustomerSegment;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

/**
 * Trait HasCustomerSegmentsOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to CustomerSegment.
 *
 * The CustomerSegment has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\CustomerSegment\HasCustomerSegments
 */
trait HasCustomerSegmentsOneToMany
{
    use HasCustomerSegmentsAbstract;

    use ReciprocatesCustomerSegment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomerSegments(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            CustomerSegment::getPlural(),
            CustomerSegment::class,
            static::getSingular()
        );
    }
}
