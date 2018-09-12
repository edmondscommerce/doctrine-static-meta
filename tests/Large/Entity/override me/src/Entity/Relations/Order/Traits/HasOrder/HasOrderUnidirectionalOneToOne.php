<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrderAbstract;

/**
 * Trait HasOrderUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One Order
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\Order\Traits\HasOrder
 */
// phpcs:enable
trait HasOrderUnidirectionalOneToOne
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
        $builder->addOwningOneToOne(
            Order::getDoctrineStaticMeta()->getSingular(),
            Order::class
        );
    }
}
