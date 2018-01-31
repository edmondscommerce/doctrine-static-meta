<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product;

interface ReciprocatesProduct
{
    public function reciprocateRelationOnProduct(Product $product): UsesPHPMetaDataInterface;

    public function removeRelationOnProduct(Product $product): UsesPHPMetaDataInterface;
}
