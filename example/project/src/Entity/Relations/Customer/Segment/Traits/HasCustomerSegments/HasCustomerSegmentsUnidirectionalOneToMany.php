<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegments;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegmentsAbstract;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

/**
 * Trait HasCustomerSegmentsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to CustomerSegment.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\CustomerSegment\HasCustomerSegments
 */
trait HasCustomerSegmentsUnidirectionalOneToMany
{
    use HasCustomerSegmentsAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomerSegments(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            CustomerSegment::getPlural(),
            CustomerSegment::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.CustomerSegment::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            CustomerSegment::getSingular().'_'.CustomerSegment::getIdField(),
            CustomerSegment::getIdField()
        );
        $manyToManyBuilder->build();

    }
}
