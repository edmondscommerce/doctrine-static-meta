<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Traits\HasProducts;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductsAbstract;
use  My\Test\Project\Entities\Relations\Product\Traits\ReciprocatesProduct;
use My\Test\Project\Entities\Product;

/**
 * Trait HasProductsOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Product.
 *
 * The Product has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Product\HasProducts
 */
trait HasProductsOneToMany
{
    use HasProductsAbstract;

    use ReciprocatesProduct;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForProducts(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            Product::getPlural(),
            Product::class,
            static::getSingular()
        );
    }
}
