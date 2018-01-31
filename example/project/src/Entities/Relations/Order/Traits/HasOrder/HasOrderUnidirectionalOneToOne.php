<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\Traits\HasOrder;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order;
use  My\Test\Project\Entities\Relations\Order\Traits\HasOrderAbstract;

trait HasOrderUnidirectionalOneToOne
{
    use HasOrderAbstract;

    public static function getPropertyMetaForOrder(ClassMetadataBuilder $builder)
    {
        $builder->addOwningOneToOne(
            Order::getSingular(),
            Order::class
        );
    }
}
