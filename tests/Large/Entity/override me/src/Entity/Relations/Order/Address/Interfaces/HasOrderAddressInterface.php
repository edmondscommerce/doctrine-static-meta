<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Order\AddressInterface;

interface HasOrderAddressInterface
{
    public const PROPERTY_NAME_ORDER_ADDRESS = 'orderAddress';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForOrderAddress(ClassMetadataBuilder $builder): void;

    /**
     * @return null|AddressInterface
     */
    public function getOrderAddress(): ?AddressInterface;

    /**
     * @param AddressInterface|null $orderAddress
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrderAddress(
        ?AddressInterface $orderAddress,
        bool $recip = true
    ): HasOrderAddressInterface;

    /**
     * @param null|AddressInterface $orderAddress
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrderAddress(
        ?AddressInterface $orderAddress = null,
        bool $recip = true
    ): HasOrderAddressInterface;
}
