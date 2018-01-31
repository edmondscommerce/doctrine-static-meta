<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Traits\HasProduct;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Product;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductAbstract;

/**
 * Trait HasProductManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Product\HasProduct
 */
trait HasProductUnidirectionalManyToOne
{
    use HasProductAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForProducts(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Product::getSingular(),
            Product::class
        );
    }
}
