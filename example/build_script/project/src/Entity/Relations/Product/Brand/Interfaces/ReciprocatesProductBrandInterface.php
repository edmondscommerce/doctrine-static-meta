<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

interface ReciprocatesProductBrandInterface
{
    /**
     * @param ProductBrand $productBrand
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnProductBrand(
        ProductBrand $productBrand
    ): UsesPHPMetaDataInterface;

    /**
     * @param ProductBrand $productBrand
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnProductBrand(
        ProductBrand $productBrand
    ): UsesPHPMetaDataInterface;
}
