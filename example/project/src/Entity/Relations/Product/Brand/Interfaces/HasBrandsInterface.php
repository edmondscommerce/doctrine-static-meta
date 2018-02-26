<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand;

interface HasBrandsInterface
{
    public const PROPERTY_NAME_BRANDS = 'brands';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForBrands(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|Brand[]
     */
    public function getBrands(): Collection;

    /**
     * @param Collection|Brand[] $brands
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setBrands(Collection $brands): UsesPHPMetaDataInterface;

    /**
     * @param Brand $brand
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @param Brand $brand
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeBrand(Brand $brand, bool $recip = true): UsesPHPMetaDataInterface;
}
