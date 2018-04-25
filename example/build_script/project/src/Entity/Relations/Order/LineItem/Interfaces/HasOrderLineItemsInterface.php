<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

interface HasOrderLineItemsInterface
{
    public const PROPERTY_NAME_ORDER_LINE_ITEMS = 'orderLineItems';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForOrderLineItems(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|OrderLineItem[]
     */
    public function getOrderLineItems(): Collection;

    /**
     * @param Collection|OrderLineItem[] $orderLineItems
     *
     * @return self
     */
    public function setOrderLineItems(Collection $orderLineItems);

    /**
     * @param OrderLineItem $orderLineItem
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrderLineItem(
        OrderLineItem $orderLineItem,
        bool $recip = true
    );

    /**
     * @param OrderLineItem $orderLineItem
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrderLineItem(
        OrderLineItem $orderLineItem,
        bool $recip = true
    );

}
