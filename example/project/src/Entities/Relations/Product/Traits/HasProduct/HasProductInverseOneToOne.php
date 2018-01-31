<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Traits\HasProduct;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Traits\ReciprocatesProduct;
use My\Test\Project\Entities\Product;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductAbstract;

trait HasProductInverseOneToOne
{
    use HasProductAbstract;

    use ReciprocatesProduct;

    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder)
    {
        $builder->addInverseOneToOne(
            Product::getSingular(),
            Product::class,
            static::getSingular()
        );
    }
}
