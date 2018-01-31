<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Traits\HasProducts;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Traits\ReciprocatesProduct;
use My\Test\Project\Entities\Product;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductsAbstract;

trait HasProductsInverseManyToMany
{
    use HasProductsAbstract;

    use ReciprocatesProduct;

    public static function getPropertyMetaForProducts(ClassMetadataBuilder $builder)
    {
        $builder = $builder->createManyToMany(
            Product::getPlural(), Product::class
        );
        $builder->mappedBy(static::getPlural());
        $builder->setJoinTable(Product::getPlural() . '_to_' . static::getPlural());
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
