<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrderAbstract;
use My\Test\Project\Entity\Relations\Order\Traits\ReciprocatesOrder;

/**
 * Trait HasOrderOwningOneToOne
 *
 * The owning side of a One to One relationship between the Current Entity
 * and Order
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\Order\Traits\HasOrder
 */
// phpcs:enable
trait HasOrderOwningOneToOne
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
        $builder->addOwningOneToOne(
            Order::getDoctrineStaticMeta()->getSingular(),
            Order::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
