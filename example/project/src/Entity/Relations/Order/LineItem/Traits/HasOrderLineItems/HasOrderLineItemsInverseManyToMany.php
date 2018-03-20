<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItems;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItemsAbstract;
use  My\Test\Project\Entity\Relations\Order\LineItem\Traits\ReciprocatesOrderLineItem;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

trait HasOrderLineItemsInverseManyToMany
{
    use HasOrderLineItemsAbstract;

    use ReciprocatesOrderLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForOrderLineItems(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            OrderLineItem::getPlural(), OrderLineItem::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(OrderLineItem::getPlural().'_to_'.static::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            OrderLineItem::getSingular().'_'.OrderLineItem::getIdField(),
            OrderLineItem::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
