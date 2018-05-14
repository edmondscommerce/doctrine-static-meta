<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Traits\HasOrder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrderAbstract;
use My\Test\Project\Entities\Order as Order;

trait HasOrderUnidirectionalOneToOne
{
    use HasOrderAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrder(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Order::getSingular(),
            Order::class
        );
    }
}
