<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasLineItems;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasLineItemsAbstract;
use  My\Test\Project\Entity\Relations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;

/**
 * Trait HasLineItemsOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to LineItem.
 *
 * The LineItem has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\LineItem\HasLineItems
 */
trait HasLineItemsOneToMany
{
    use HasLineItemsAbstract;

    use ReciprocatesLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForLineItems(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            LineItem::getPlural(),
            LineItem::class,
            static::getSingular()
        );
    }
}
