<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItem;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItemAbstract;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

trait HasOrderLineItemUnidirectionalOneToOne
{
    use HasOrderLineItemAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderLineItem(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            OrderLineItem::getSingular(),
            OrderLineItem::class
        );
    }
}
