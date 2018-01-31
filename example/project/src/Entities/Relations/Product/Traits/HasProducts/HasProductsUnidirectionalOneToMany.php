<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Traits\HasProducts;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Product;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductsAbstract;

/**
 * Trait HasProductsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Product.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Product\HasProducts
 */
trait HasProductsUnidirectionalOneToMany
{
    use HasProductsAbstract;

    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            Product::getPlural(),
            Product::class
        );
    }
}
