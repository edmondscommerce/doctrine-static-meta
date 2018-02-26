<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrders;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\Traits\HasOrdersAbstract;
use  My\Test\Project\Entity\Relations\Order\Traits\ReciprocatesOrder;
use My\Test\Project\Entities\Order;

trait HasOrdersOwningManyToMany
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

        $manyToManyBuilder = $builder->createManyToMany(
            Order::getPlural(), Order::class
        );
        $manyToManyBuilder->inversedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(static::getPlural().'_to_'.Order::getPlural());
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
