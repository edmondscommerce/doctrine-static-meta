<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegment;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\ReciprocatesSegment;
use My\Test\Project\Entities\Customer\Segment;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegmentAbstract;

trait HasSegmentInverseOneToOne
{
    use HasSegmentAbstract;

    use ReciprocatesSegment;

    public static function getPropertyMetaForSegment(ClassMetadataBuilder $builder)
    {
        $builder->addInverseOneToOne(
            Segment::getSingular(),
            Segment::class,
            static::getSingular()
        );
    }
}
