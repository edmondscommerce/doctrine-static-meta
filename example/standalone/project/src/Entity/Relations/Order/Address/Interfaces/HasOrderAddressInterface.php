<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address as OrderAddress;

interface HasOrderAddressInterface
{
    public const PROPERTY_NAME_ORDER_ADDRESS = 'orderAddress';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForOrderAddress(ClassMetadataBuilder $builder): void;

    /**
     * @return null|OrderAddress
     */
    public function getOrderAddress(): ?OrderAddress;

    /**
     * @param OrderAddress|null $orderAddress
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrderAddress(
        ?OrderAddress $orderAddress,
        bool $recip = true
    ): HasOrderAddressInterface;

    /**
     * @return self
     */
    public function removeOrderAddress(): HasOrderAddressInterface;
}