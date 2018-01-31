<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItems;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItemsAbstract;

/**
 * Trait HasLineItemsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to LineItem.
 *
 * @package My\Test\Project\Entities\Traits\Relations\LineItem\HasLineItems
 */
trait HasLineItemsUnidirectionalOneToMany
{
    use HasLineItemsAbstract;

    public static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            LineItem::getPlural(),
            LineItem::class
        );
    }
}
