<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Address\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address;

interface HasAddress
{
    public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder);

    public function getAddress(): ?Address;

    public function setAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeAddress(): UsesPHPMetaDataInterface;

}
