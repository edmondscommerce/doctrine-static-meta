<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegments;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer\Segment;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegmentsAbstract;

/**
 * Trait HasSegmentsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Segment.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Segment\HasSegments
 */
trait HasSegmentsUnidirectionalOneToMany
{
    use HasSegmentsAbstract;

    public static function getPropertyMetaForSegment(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            Segment::getPlural(),
            Segment::class
        );
    }
}
