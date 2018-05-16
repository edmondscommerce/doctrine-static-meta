<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order as Order;

interface HasOrderInterface
{
    public const PROPERTY_NAME_ORDER = 'order';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForOrder(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Order
     */
    public function getOrder(): ?Order;

    /**
     * @param Order|null $order
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrder(
        ?Order $order,
        bool $recip = true
    ): HasOrderInterface;

    /**
     * @return self
     */
    public function removeOrder(): HasOrderInterface;
}