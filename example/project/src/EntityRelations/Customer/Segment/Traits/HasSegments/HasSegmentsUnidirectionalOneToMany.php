<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Segment\Traits\HasSegments;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Customer\Segment\Traits\HasSegmentsAbstract;
use My\Test\Project\Entities\Customer\Segment;

/**
 * Trait HasSegmentsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Segment.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\Segment\HasSegments
 */
trait HasSegmentsUnidirectionalOneToMany
{
    use HasSegmentsAbstract;

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
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.Segment::getPlural());
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
