<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItem;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemAbstract;

/**
 * Trait HasLineItemManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of LineItem.
 *
 * LineItem has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\LineItem\HasLineItem
 */
trait HasLineItemManyToOne
{
    use HasLineItemAbstract;

    use ReciprocatesLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            LineItem::getSingular(),
            LineItem::class,
            static::getPlural()
        );
    }
}
