<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItems;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemsAbstract;
use My\Test\Project\Entities\Order\LineItem;

/**
 * Trait HasLineItemsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to LineItem.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\LineItem\HasLineItems
 */
trait HasLineItemsUnidirectionalOneToMany
{
    use HasLineItemsAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForLineItems(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            LineItem::getPlural(),
            LineItem::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.LineItem::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            LineItem::getSingular().'_'.LineItem::getIdField(),
            LineItem::getIdField()
        );
        $manyToManyBuilder->build();

    }
}
