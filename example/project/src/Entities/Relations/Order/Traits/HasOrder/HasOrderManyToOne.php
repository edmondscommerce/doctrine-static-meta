<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Traits\HasOrder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\Traits\ReciprocatesOrder;
use My\Test\Project\Entities\Order;
use  My\Test\Project\Entities\Relations\Order\Traits\HasOrderAbstract;

/**
 * Trait HasOrderManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of Order.
 *
 * Order has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Order\HasOrder
 */
trait HasOrderManyToOne
{
    use HasOrderAbstract;

    use ReciprocatesOrder;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForOrder(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Order::getSingular(),
            Order::class,
            static::getPlural()
        );
    }
}
