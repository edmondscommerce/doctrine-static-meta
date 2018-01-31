<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\Traits\HasOrders;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\Traits\ReciprocatesOrder;
use My\Test\Project\Entities\Order;
use  My\Test\Project\Entities\Relations\Order\Traits\HasOrdersAbstract;

trait HasOrdersInverseManyToMany
{
    use HasOrdersAbstract;

    use ReciprocatesOrder;

    public static function getPropertyMetaForOrders(ClassMetadataBuilder $builder)
    {
        $builder = $builder->createManyToMany(
            Order::getPlural(), Order::class
        );
        $builder->mappedBy(static::getPlural());
        $builder->setJoinTable(Order::getPlural() . '_to_' . static::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            Order::getSingular() . '_' . Order::getIdField(),
            Order::getIdField()
        );
        $builder->build();
    }
}
