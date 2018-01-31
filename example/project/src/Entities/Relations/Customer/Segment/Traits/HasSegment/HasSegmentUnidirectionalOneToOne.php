<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegment;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Customer\Segment;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegmentAbstract;

trait HasSegmentUnidirectionalOneToOne
{
    use HasSegmentAbstract;

    public static function getPropertyMetaForSegment(ClassMetadataBuilder $builder)
    {
        $builder->addOwningOneToOne(
            Segment::getSingular(),
            Segment::class
        );
    }
}
