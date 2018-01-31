<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Product\Brand;

interface ReciprocatesBrand
{
    public function reciprocateRelationOnBrand(Brand $brand): UsesPHPMetaDataInterface;

    public function removeRelationOnBrand(Brand $brand): UsesPHPMetaDataInterface;
}
