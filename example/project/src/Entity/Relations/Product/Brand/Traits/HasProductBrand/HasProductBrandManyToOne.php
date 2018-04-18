<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrand;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\ReciprocatesProductBrand;
use My\Test\Project\Entities\Product\Brand as ProductBrand;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrandAbstract;

/**
 * Trait HasProductBrandManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of ProductBrand.
 *
 * ProductBrand has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @package My\Test\Project\Entities\Traits\Relations\ProductBrand\HasProductBrand
 */
trait HasProductBrandManyToOne
{
    use HasProductBrandAbstract;

    use ReciprocatesProductBrand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForProductBrand(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            ProductBrand::getSingular(),
            ProductBrand::class,
            static::getPlural()
        );
    }
}
