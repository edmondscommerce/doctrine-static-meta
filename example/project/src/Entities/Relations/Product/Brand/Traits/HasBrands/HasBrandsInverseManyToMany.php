<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrands;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandsAbstract;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\ReciprocatesBrand;
use My\Test\Project\Entities\Product\Brand;

trait HasBrandsInverseManyToMany
{
    use HasBrandsAbstract;

    use ReciprocatesBrand;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForBrands(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Brand::getPlural(), Brand::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(Brand::getPlural().'_to_'.static::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Brand::getSingular().'_'.Brand::getIdField(),
            Brand::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
