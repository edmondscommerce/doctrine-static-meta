<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Customer\Segment\Traits\HasSegments;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Customer\Segment\Traits\HasSegmentsAbstract;
use My\Test\Project\EntityRelations\Customer\Segment\Traits\ReciprocatesSegment;
use My\Test\Project\Entities\Customer\Segment;

trait HasSegmentsOwningManyToMany
{
    use HasSegmentsAbstract;

    use ReciprocatesSegment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForSegments(ClassMetadataBuilder $builder): void
    {

        $manyToManyBuilder = $builder->createManyToMany(
            Segment::getPlural(),
            Segment::class
        );
        $manyToManyBuilder->inversedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(static::getPlural().'_to_'.Segment::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Segment::getSingular().'_'.Segment::getIdField(),
            Segment::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
