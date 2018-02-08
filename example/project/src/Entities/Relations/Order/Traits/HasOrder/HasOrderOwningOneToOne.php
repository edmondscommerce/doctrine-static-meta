<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\Traits\HasOrder;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\Traits\ReciprocatesOrder;
use My\Test\Project\Entities\Order;
use  My\Test\Project\Entities\Relations\Order\Traits\HasOrderAbstract;

trait HasOrderOwningOneToOne
{
    use HasOrderAbstract;

    use ReciprocatesOrder;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForOrder(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Order::getSingular(),
            Order::class,
            static::getSingular()
        );
    }
}
