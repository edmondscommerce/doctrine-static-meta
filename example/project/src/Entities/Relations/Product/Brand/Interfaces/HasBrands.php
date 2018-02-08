<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand;

interface HasBrands
{
    public static function getPropertyMetaForBrands(ClassMetadataBuilder $builder);

    public function getBrands(): Collection;

    public function setBrands(Collection $brands): UsesPHPMetaDataInterface;

    public function addBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface;

}
