<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrders;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrdersAbstract;
use My\Test\Project\Entities\Order;

/**
 * Trait HasOrdersUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Order.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\Order\HasOrders
 */
trait HasOrdersUnidirectionalOneToMany
{
    use HasOrdersAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrders(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Order::getPlural(),
            Order::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.Order::getPlural());
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
