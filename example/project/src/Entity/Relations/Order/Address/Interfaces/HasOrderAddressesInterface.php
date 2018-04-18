<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address as OrderAddress;

interface HasOrderAddressesInterface
{
    public const PROPERTY_NAME_ORDER_ADDRESSES = 'orderAddresses';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForOrderAddresses(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|OrderAddress[]
     */
    public function getOrderAddresses(): Collection;

    /**
     * @param Collection|OrderAddress[] $orderAddresses
     *
     * @return self
     */
    public function setOrderAddresses(Collection $orderAddresses);

    /**
     * @param OrderAddress $orderAddress
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrderAddress(
        OrderAddress $orderAddress,
        bool $recip = true
    );

    /**
     * @param OrderAddress $orderAddress
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrderAddress(
        OrderAddress $orderAddress,
        bool $recip = true
    );
}
