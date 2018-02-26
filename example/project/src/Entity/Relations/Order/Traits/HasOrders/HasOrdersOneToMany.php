<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrdersAbstract;
use My\Test\Project\Entity\Relations\Order\Traits\ReciprocatesOrder;
use My\Test\Project\Entities\Order;

/**
 * Trait HasOrdersOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Order.
 *
 * The Order has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Order\HasOrders
 */
trait HasOrdersOneToMany
{
    use HasOrdersAbstract;

    use ReciprocatesOrder;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrders(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            Order::getPlural(),
            Order::class,
            static::getSingular()
        );
    }
}
