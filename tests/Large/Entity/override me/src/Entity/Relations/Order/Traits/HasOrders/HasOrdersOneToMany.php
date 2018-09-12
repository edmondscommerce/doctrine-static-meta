<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrdersAbstract;
use My\Test\Project\Entity\Relations\Order\Traits\ReciprocatesOrder;

/**
 * Trait HasOrdersOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to Order.
 *
 * The Order has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\Order\HasOrders
 */
// phpcs:enable
trait HasOrdersOneToMany
{
    use HasOrdersAbstract;

    use ReciprocatesOrder;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForOrders(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            Order::getDoctrineStaticMeta()->getPlural(),
            Order::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
