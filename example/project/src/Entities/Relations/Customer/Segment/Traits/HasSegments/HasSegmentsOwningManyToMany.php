<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegments;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\ReciprocatesSegment;
use My\Test\Project\Entities\Customer\Segment;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegmentsAbstract;

trait HasSegmentsOwningManyToMany
{
    use HasSegmentsAbstract;

    use ReciprocatesSegment;

    public static function getPropertyMetaForSegments(ClassMetadataBuilder $builder)
    {

        $builder = $builder->createManyToMany(
            Segment::getPlural(), Segment::class
        );
        $builder->inversedBy(static::getPlural());
        $builder->setJoinTable(static::getPlural() . '_to_' . Segment::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            Segment::getSingular() . '_' . Segment::getIdField(),
            Segment::getIdField()
        );
        $builder->build();
    }
}
