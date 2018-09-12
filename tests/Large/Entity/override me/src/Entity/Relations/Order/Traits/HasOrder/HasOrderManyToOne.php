<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrderAbstract;
use My\Test\Project\Entity\Relations\Order\Traits\ReciprocatesOrder;

/**
 * Trait HasOrderManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of Order.
 *
 * Order has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\Order\HasOrder
 */
// phpcs:enable
trait HasOrderManyToOne
{
    use HasOrderAbstract;

    use ReciprocatesOrder;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForOrder(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            Order::getDoctrineStaticMeta()->getSingular(),
            Order::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
