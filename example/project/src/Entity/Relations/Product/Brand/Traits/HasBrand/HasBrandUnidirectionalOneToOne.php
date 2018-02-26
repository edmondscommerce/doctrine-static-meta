<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasBrand;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\HasBrandAbstract;
use My\Test\Project\Entities\Product\Brand;

trait HasBrandUnidirectionalOneToOne
{
    use HasBrandAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForBrand(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Brand::getSingular(),
            Brand::class
        );
    }
}
