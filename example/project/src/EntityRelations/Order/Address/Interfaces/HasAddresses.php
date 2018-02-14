<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\Address\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address;

interface HasAddresses
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection;

    /**
     * @param Collection|Address[] $addresses
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setAddresses(Collection $addresses): UsesPHPMetaDataInterface;

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface;

}
