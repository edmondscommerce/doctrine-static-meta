<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegments;


use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegmentsAbstract;
use  My\Test\Project\Entity\Relations\Customer\Segment\Traits\ReciprocatesCustomerSegment;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;

trait HasCustomerSegmentsInverseManyToMany
{
    use HasCustomerSegmentsAbstract;

    use ReciprocatesCustomerSegment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomerSegments(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            CustomerSegment::getPlural(), CustomerSegment::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $fromTableName = Inflector::tableize(CustomerSegment::getPlural());
        $toTableName   = Inflector::tableize(static::getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
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
