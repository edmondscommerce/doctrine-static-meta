<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Traits\HasProduct;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Traits\ReciprocatesProduct;
use My\Test\Project\Entities\Product;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductAbstract;

/**
 * Trait HasProductManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of Product.
 *
 * Product has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Product\HasProduct
 */
trait HasProductManyToOne
{
    use HasProductAbstract;

    use ReciprocatesProduct;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Product::getSingular(),
            Product::class,
            static::getPlural()
        );
    }
}
