<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Traits\HasOrders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order;
use  My\Test\Project\Entities\Relations\Order\Traits\HasOrdersAbstract;

/**
 * Trait HasOrdersUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Order.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Order\HasOrders
 */
trait HasOrdersUnidirectionalOneToMany
{
    use HasOrdersAbstract;

    public static function getPropertyMetaForOrder(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            Order::getPlural(),
            Order::class
        );
    }
}
