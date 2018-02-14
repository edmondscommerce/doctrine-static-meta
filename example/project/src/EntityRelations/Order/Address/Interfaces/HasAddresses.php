<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\Address\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address;

interface HasAddresses
{
    public static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder);

    public function getAddresses(): Collection;

    public function setAddresses(Collection $addresses): UsesPHPMetaDataInterface;

    public function addAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface;

}
