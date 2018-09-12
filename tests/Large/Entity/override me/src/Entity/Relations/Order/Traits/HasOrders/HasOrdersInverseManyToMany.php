<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrders;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrdersAbstract;
use My\Test\Project\Entity\Relations\Order\Traits\ReciprocatesOrder;

/**
 * Trait HasOrdersInverseManyToMany
 *
 * The inverse side of a Many to Many relationship between the Current Entity
 * And Order
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\Order\Traits\HasOrders
 */
// phpcs:enable
trait HasOrdersInverseManyToMany
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
        $manyToManyBuilder = $builder->createManyToMany(
            Order::getDoctrineStaticMeta()->getPlural(),
            Order::class
        );
        $manyToManyBuilder->mappedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(Order::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                Order::getDoctrineStaticMeta()->getSingular() . '_' . Order::PROP_ID
            ),
            Order::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
