<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\OrderInterface;

interface HasOrdersInterface
{
    public const PROPERTY_NAME_ORDERS = 'orders';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForOrders(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|OrderInterface[]
     */
    public function getOrders(): Collection;

    /**
     * @param Collection|OrderInterface[] $orders
     *
     * @return self
     */
    public function setOrders(Collection $orders): self;

    /**
     * @param OrderInterface|null $order
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrder(
        ?OrderInterface $order,
        bool $recip = true
    ): HasOrdersInterface;

    /**
     * @param OrderInterface $order
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrder(
        OrderInterface $order,
        bool $recip = true
    ): HasOrdersInterface;

}
