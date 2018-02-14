<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Customer\Segment\Traits\HasSegments;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Customer\Segment\Traits\HasSegmentsAbstract;
use My\Test\Project\EntityRelations\Customer\Segment\Traits\ReciprocatesSegment;
use My\Test\Project\Entities\Customer\Segment;

trait HasSegmentsInverseManyToMany
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
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(Segment::getPlural().'_to_'.static::getPlural());
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
