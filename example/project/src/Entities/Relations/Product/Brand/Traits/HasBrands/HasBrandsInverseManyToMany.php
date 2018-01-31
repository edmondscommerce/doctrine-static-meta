<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrands;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\ReciprocatesBrand;
use My\Test\Project\Entities\Product\Brand;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandsAbstract;

trait HasBrandsInverseManyToMany
{
    use HasBrandsAbstract;

    use ReciprocatesBrand;

    public static function getPropertyMetaForBrands(ClassMetadataBuilder $builder)
    {
        $builder = $builder->createManyToMany(
            Brand::getPlural(), Brand::class
        );
        $builder->mappedBy(static::getPlural());
        $builder->setJoinTable(Brand::getPlural() . '_to_' . static::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            Brand::getSingular() . '_' . Brand::getIdField(),
            Brand::getIdField()
        );
        $builder->build();
    }
}
