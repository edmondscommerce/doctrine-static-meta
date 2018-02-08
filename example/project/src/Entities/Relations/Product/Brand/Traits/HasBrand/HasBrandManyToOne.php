<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrand;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\ReciprocatesBrand;
use My\Test\Project\Entities\Product\Brand;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandAbstract;

/**
 * Trait HasBrandManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to One instance of Brand.
 *
 * Brand has a corresponding OneToMany relationship to the current Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\Brand\HasBrand
 */
trait HasBrandManyToOne
{
    use HasBrandAbstract;

    use ReciprocatesBrand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForBrand(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Brand::getSingular(),
            Brand::class,
            static::getPlural()
        );
    }
}
