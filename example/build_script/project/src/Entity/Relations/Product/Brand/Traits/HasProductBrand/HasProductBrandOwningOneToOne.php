<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrand;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\ReciprocatesProductBrand;
use My\Test\Project\Entities\Product\Brand as ProductBrand;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrandAbstract;

trait HasProductBrandOwningOneToOne
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
        $builder->addOwningOneToOne(
            ProductBrand::getSingular(),
            ProductBrand::class,
            static::getSingular()
        );
    }
}
