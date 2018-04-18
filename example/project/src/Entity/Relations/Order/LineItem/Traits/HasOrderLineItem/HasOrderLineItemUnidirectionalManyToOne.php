<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItem;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItemAbstract;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

/**
 * Trait HasOrderLineItemManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\OrderLineItem\HasOrderLineItem
 */
trait HasOrderLineItemUnidirectionalManyToOne
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
        $builder->addManyToOne(
            OrderLineItem::getSingular(),
            OrderLineItem::class
        );
    }
}
