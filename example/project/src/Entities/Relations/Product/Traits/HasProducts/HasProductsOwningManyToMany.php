<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Traits\HasProducts;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Traits\ReciprocatesProduct;
use My\Test\Project\Entities\Product;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductsAbstract;

trait HasProductsOwningManyToMany
{
    use HasProductsAbstract;

    use ReciprocatesProduct;

    public static function getPropertyMetaForProducts(ClassMetadataBuilder $builder)
    {

        $builder = $builder->createManyToMany(
            Product::getPlural(), Product::class
        );
        $builder->inversedBy(static::getPlural());
        $builder->setJoinTable(static::getPlural() . '_to_' . Product::getPlural());
        $builder->addJoinColumn(
            static::getSingular() . '_' . static::getIdField(),
            static::getIdField()
        );
        $builder->addInverseJoinColumn(
            Product::getSingular() . '_' . Product::getIdField(),
            Product::getIdField()
        );
        $builder->build();
    }
}
