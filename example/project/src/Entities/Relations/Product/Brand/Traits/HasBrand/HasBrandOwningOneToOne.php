<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrand;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\ReciprocatesBrand;
use My\Test\Project\Entities\Product\Brand;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandAbstract;

trait HasBrandOwningOneToOne
{
    use HasBrandAbstract;

    use ReciprocatesBrand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForBrand(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Brand::getSingular(),
            Brand::class,
            static::getSingular()
        );
    }
}
