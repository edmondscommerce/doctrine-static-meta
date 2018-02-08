<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrand;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrandAbstract;
use My\Test\Project\Entities\Product\Brand;

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
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForBrand(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Brand::getSingular(),
            Brand::class
        );
    }
}
