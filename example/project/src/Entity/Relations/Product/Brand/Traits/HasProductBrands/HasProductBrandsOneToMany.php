<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrands;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrandsAbstract;
use My\Test\Project\Entity\Relations\Product\Brand\Traits\ReciprocatesProductBrand;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

/**
 * Trait HasProductBrandsOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to ProductBrand.
 *
 * The ProductBrand has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\ProductBrand\HasProductBrands
 */
trait HasProductBrandsOneToMany
{
    use HasProductBrandsAbstract;

    use ReciprocatesProductBrand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForProductBrands(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            ProductBrand::getPlural(),
            ProductBrand::class,
            static::getSingular()
        );
    }
}
