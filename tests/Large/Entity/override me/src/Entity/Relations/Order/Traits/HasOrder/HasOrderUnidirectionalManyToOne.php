<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrderAbstract;



/**
 * Trait HasOrderManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of Order
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\Order\HasOrder
 */
// phpcs:enable
trait HasOrderUnidirectionalManyToOne
{
    use HasOrderAbstract;

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
            Order::class
        );
    }
}
