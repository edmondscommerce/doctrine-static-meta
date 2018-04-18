<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;

interface HasOrderLineItemInterface
{
    public const PROPERTY_NAME_ORDER_LINE_ITEM = 'orderLineItem';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForOrderLineItem(ClassMetadataBuilder $builder): void;

    /**
     * @return null|OrderLineItem
     */
    public function getOrderLineItem(): ?OrderLineItem;

    /**
     * @param OrderLineItem $orderLineItem
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrderLineItem(
        OrderLineItem $orderLineItem,
        bool $recip = true
    );

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeOrderLineItem();
}
