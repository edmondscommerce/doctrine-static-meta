<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\Address\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address;

interface HasAddress
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Address
     */
    public function getAddress(): ?Address;

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeAddress(): UsesPHPMetaDataInterface;
}
