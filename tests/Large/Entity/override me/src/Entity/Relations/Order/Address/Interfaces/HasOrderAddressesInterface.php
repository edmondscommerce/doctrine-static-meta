<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Order\AddressInterface;

interface HasOrderAddressesInterface
{
    public const PROPERTY_NAME_ORDER_ADDRESSES = 'orderAddresses';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForOrderAddresses(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|AddressInterface[]
     */
    public function getOrderAddresses(): Collection;

    /**
     * @param Collection|AddressInterface[] $orderAddresses
     *
     * @return self
     */
    public function setOrderAddresses(Collection $orderAddresses): self;

    /**
     * @param AddressInterface|null $orderAddress
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrderAddress(
        ?AddressInterface $orderAddress,
        bool $recip = true
    ): HasOrderAddressesInterface;

    /**
     * @param AddressInterface $orderAddress
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrderAddress(
        AddressInterface $orderAddress,
        bool $recip = true
    ): HasOrderAddressesInterface;

}
