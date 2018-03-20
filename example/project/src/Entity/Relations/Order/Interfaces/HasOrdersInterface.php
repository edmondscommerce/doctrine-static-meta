<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order as Order;

interface HasOrdersInterface
{
    public const PROPERTY_NAME_ORDERS = 'orders';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForOrders(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection;

    /**
     * @param Collection|Order[] $orders
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setOrders(Collection $orders): UsesPHPMetaDataInterface;

    /**
     * @param Order $order
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrder(
        Order $order,
        bool $recip = true
    ): UsesPHPMetaDataInterface;

    /**
     * @param Order $order
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrder(
        Order $order,
        bool $recip = true
    ): UsesPHPMetaDataInterface;

}
