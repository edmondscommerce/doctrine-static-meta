<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrands;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\ReciprocatesBrand;
use My\Test\Project\Entities\Product\Brand;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandsAbstract;

trait HasBrandsOwningManyToMany
{
    use HasBrandsAbstract;

    use ReciprocatesBrand;

    public static function getPropertyMetaForBrands(ClassMetadataBuilder $builder)
    {

        $builder = $builder->createManyToMany(
            Brand::getPlural(), Brand::class
        );
        $builder->inversedBy(static::getPlural());
        $builder->setJoinTable(static::getPlural() . '_to_' . Brand::getPlural());
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
