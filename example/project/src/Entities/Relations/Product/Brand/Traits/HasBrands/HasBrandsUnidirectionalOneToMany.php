<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrands;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Product\Brand;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandsAbstract;

/**
 * Trait HasBrandsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Brand.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Brand\HasBrands
 */
trait HasBrandsUnidirectionalOneToMany
{
    use HasBrandsAbstract;

    public static function getPropertyMetaForBrand(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            Brand::getPlural(),
            Brand::class
        );
    }
}
