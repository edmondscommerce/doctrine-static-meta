<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrands;


use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrandsAbstract;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\ReciprocatesProductBrand;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

trait HasProductBrandsInverseManyToMany
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
        $manyToManyBuilder = $builder->createManyToMany(
            ProductBrand::getPlural(), ProductBrand::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $fromTableName = Inflector::tableize(ProductBrand::getPlural());
        $toTableName   = Inflector::tableize(static::getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName.'_to_'.$toTableName);
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            ProductBrand::getSingular().'_'.ProductBrand::getIdField(),
            ProductBrand::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
