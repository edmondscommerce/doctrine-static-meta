<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Interfaces;

use My\Test\Project\Entities\Product\Brand as ProductBrand;

interface ReciprocatesProductBrandInterface
{
    /**
     * @param ProductBrand $productBrand
     *
     * @return self
     */
    public function reciprocateRelationOnProductBrand(
        ProductBrand $productBrand
    ): self;

    /**
     * @param ProductBrand $productBrand
     *
     * @return self
     */
    public function removeRelationOnProductBrand(
        ProductBrand $productBrand
    ): self;
}
