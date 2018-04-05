<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItems;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItemsAbstract;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;
use Doctrine\Common\Inflector\Inflector;

/**
 * Trait HasOrderLineItemsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to OrderLineItem.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\OrderLineItem\HasOrderLineItems
 */
trait HasOrderLineItemsUnidirectionalOneToMany
{
    use HasOrderLineItemsAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderLineItems(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            OrderLineItem::getPlural(),
            OrderLineItem::class
        );
        $fromTableName = Inflector::tableize(static::getSingular());
        $toTableName   = Inflector::tableize(OrderLineItem::getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            OrderLineItem::getSingular().'_'.OrderLineItem::getIdField(),
            OrderLineItem::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
