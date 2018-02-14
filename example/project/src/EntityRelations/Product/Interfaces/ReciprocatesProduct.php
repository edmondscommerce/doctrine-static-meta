<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product;

interface ReciprocatesProduct
{
    /**
     * @param Product $product
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnProduct(Product $product): UsesPHPMetaDataInterface;

    /**
     * @param Product $product
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnProduct(Product $product): UsesPHPMetaDataInterface;
}
