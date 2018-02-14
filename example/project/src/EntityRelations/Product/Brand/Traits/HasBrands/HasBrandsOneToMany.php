<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Brand\Traits\HasBrands;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\EntityRelations\Product\Brand\Traits\HasBrandsAbstract;
use  My\Test\Project\EntityRelations\Product\Brand\Traits\ReciprocatesBrand;
use My\Test\Project\Entities\Product\Brand;

/**
 * Trait HasBrandsOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Brand.
 *
 * The Brand has a corresponding ManyToOne relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Brand\HasBrands
 */
trait HasBrandsOneToMany
{
    use HasBrandsAbstract;

    use ReciprocatesBrand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForBrands(ClassMetadataBuilder $builder): void
    {
        $builder->addOneToMany(
            Brand::getPlural(),
            Brand::class,
            static::getSingular()
        );
    }
}
