<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

interface HasProductBrandsInterface
{
    public const PROPERTY_NAME_PRODUCT_BRANDS = 'productBrands';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForProductBrands(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|ProductBrand[]
     */
    public function getProductBrands(): Collection;

    /**
     * @param Collection|ProductBrand[] $productBrands
     *
     * @return self
     */
    public function setProductBrands(Collection $productBrands);

    /**
     * @param ProductBrand $productBrand
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addProductBrand(
        ProductBrand $productBrand,
        bool $recip = true
    );

    /**
     * @param ProductBrand $productBrand
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeProductBrand(
        ProductBrand $productBrand,
        bool $recip = true
    );

}
