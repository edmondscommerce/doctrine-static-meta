<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrand;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Product\Brand;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandAbstract;

trait HasBrandUnidirectionalOneToOne
{
    use HasBrandAbstract;

    public static function getPropertyMetaForBrand(ClassMetadataBuilder $builder)
    {
        $builder->addOwningOneToOne(
            Brand::getSingular(),
            Brand::class
        );
    }
}
