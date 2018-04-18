<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

interface HasProductBrandInterface
{
    public const PROPERTY_NAME_PRODUCT_BRAND = 'productBrand';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyDoctrineMetaForProductBrand(ClassMetadataBuilder $builder): void;

    /**
     * @return null|ProductBrand
     */
    public function getProductBrand(): ?ProductBrand;

    /**
     * @param ProductBrand $productBrand
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setProductBrand(
        ProductBrand $productBrand,
        bool $recip = true
    );

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeProductBrand();
}
