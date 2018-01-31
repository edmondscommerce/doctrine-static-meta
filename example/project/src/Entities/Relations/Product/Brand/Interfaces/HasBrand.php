<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand;

interface HasBrand
{
    static function getPropertyMetaForBrand(ClassMetadataBuilder $builder);

    public function getBrand(): ?Brand;

    public function setBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeBrand(): UsesPHPMetaDataInterface;

}
