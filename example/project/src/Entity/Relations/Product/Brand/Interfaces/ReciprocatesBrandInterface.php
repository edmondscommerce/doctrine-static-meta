<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand;

interface ReciprocatesBrandInterface
{
    /**
     * @param Brand $brand
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnBrand(Brand $brand): UsesPHPMetaDataInterface;

    /**
     * @param Brand $brand
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnBrand(Brand $brand): UsesPHPMetaDataInterface;
}