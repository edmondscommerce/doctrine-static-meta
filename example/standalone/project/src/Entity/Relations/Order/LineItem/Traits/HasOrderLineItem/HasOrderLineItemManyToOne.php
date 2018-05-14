<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItem;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\ReciprocatesOrderLineItem;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItemAbstract;

/**
 * Trait HasOrderLineItemManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of OrderLineItem.
 *
 * OrderLineItem has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\OrderLineItem\HasOrderLineItem
 */
trait HasOrderLineItemManyToOne
{
    use HasOrderLineItemAbstract;

    use ReciprocatesOrderLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderLineItem(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            OrderLineItem::getSingular(),
            OrderLineItem::class,
            static::getPlural()
        );
    }
}
