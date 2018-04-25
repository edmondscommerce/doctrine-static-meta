<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItems;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItemsAbstract;
use  My\Test\Project\Entity\Relations\Order\LineItem\Traits\ReciprocatesOrderLineItem;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

/**
 * Trait HasOrderLineItemsOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to OrderLineItem.
 *
 * The OrderLineItem has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\OrderLineItem\HasOrderLineItems
 */
trait HasOrderLineItemsOneToMany
{
    use HasOrderLineItemsAbstract;

    use ReciprocatesOrderLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderLineItems(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            OrderLineItem::getPlural(),
            OrderLineItem::class,
            static::getSingular()
        );
    }
}
