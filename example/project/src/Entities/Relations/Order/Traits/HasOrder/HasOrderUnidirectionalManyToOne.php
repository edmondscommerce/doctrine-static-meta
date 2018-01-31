<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Traits\HasOrder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order;
use  My\Test\Project\Entities\Relations\Order\Traits\HasOrderAbstract;

/**
 * Trait HasOrderManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Order\HasOrder
 */
trait HasOrderUnidirectionalManyToOne
{
    use HasOrderAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForOrders(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Order::getSingular(),
            Order::class
        );
    }
}
