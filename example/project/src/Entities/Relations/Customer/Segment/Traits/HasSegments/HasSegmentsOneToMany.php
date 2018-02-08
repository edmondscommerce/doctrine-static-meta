<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegments;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegmentsAbstract;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\ReciprocatesSegment;
use My\Test\Project\Entities\Customer\Segment;

/**
 * Trait HasSegmentsOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Segment.
 *
 * The Segment has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Segment\HasSegments
 */
trait HasSegmentsOneToMany
{
    use HasSegmentsAbstract;

    use ReciprocatesSegment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForSegments(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            Segment::getPlural(),
            Segment::class,
            static::getSingular()
        );
    }
}
