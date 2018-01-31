<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegment;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\ReciprocatesSegment;
use My\Test\Project\Entities\Customer\Segment;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegmentAbstract;

/**
 * Trait HasSegmentManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of Segment.
 *
 * Segment has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Segment\HasSegment
 */
trait HasSegmentManyToOne
{
    use HasSegmentAbstract;

    use ReciprocatesSegment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForSegment(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Segment::getSingular(),
            Segment::class,
            static::getPlural()
        );
    }
}
