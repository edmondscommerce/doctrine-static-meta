<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegment;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer\Segment;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegmentAbstract;

/**
 * Trait HasSegmentManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Segment\HasSegment
 */
trait HasSegmentUnidirectionalManyToOne
{
    use HasSegmentAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForSegments(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Segment::getSingular(),
            Segment::class
        );
    }
}
