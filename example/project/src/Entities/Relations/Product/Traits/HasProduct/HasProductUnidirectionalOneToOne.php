<?php declare(strict_types=1);


namespace My\Test\Project\Entities\Relations\Product\Traits\HasProduct;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Product;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductAbstract;

trait HasProductUnidirectionalOneToOne
{
    use HasProductAbstract;

    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder)
    {
        $builder->addOwningOneToOne(
            Product::getSingular(),
            Product::class
        );
    }
}
