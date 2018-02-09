<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Order\Traits\HasOrders;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Order\Traits\HasOrdersAbstract;
use  My\Test\Project\Entities\Relations\Order\Traits\ReciprocatesOrder;
use My\Test\Project\Entities\Order;

trait HasOrdersInverseManyToMany
{
    use HasOrdersAbstract;

    use ReciprocatesOrder;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForOrders(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Order::getPlural(), Order::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(Order::getPlural().'_to_'.static::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Order::getSingular().'_'.Order::getIdField(),
            Order::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
