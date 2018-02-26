<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasBrand;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Product\Brand\Traits\ReciprocatesBrand;
use My\Test\Project\Entities\Product\Brand;
use My\Test\Project\Entity\Relations\Product\Brand\Traits\HasBrandAbstract;

trait HasBrandInverseOneToOne
{
    use HasBrandAbstract;

    use ReciprocatesBrand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForBrand(ClassMetadataBuilder $builder): void
    {
        $builder->addInverseOneToOne(
            Brand::getSingular(),
            Brand::class,
            static::getSingular()
        );
    }
}
