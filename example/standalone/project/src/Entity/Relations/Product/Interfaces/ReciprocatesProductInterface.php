<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Interfaces;

use My\Test\Project\Entities\Product as Product;

interface ReciprocatesProductInterface
{
    /**
     * @param Product $product
     *
     * @return self
     */
    public function reciprocateRelationOnProduct(
        Product $product
    ): self;

    /**
     * @param Product $product
     *
     * @return self
     */
    public function removeRelationOnProduct(
        Product $product
    ): self;
}
