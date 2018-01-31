<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrand;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Product\Brand;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandAbstract;

/**
 * Trait HasBrandManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Brand\HasBrand
 */
trait HasBrandUnidirectionalManyToOne
{
    use HasBrandAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \ReflectionException
     */
    public static function getPropertyMetaForBrands(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            Brand::getSingular(),
            Brand::class
        );
    }
}
