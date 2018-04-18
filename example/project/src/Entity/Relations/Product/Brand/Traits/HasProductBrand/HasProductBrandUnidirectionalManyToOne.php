<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrand;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrandAbstract;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

/**
 * Trait HasProductBrandManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\ProductBrand\HasProductBrand
 */
trait HasProductBrandUnidirectionalManyToOne
{
    use HasProductBrandAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForProductBrand(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            ProductBrand::getSingular(),
            ProductBrand::class
        );
    }
}
