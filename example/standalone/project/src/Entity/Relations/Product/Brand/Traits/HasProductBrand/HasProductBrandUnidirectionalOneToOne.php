<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrand;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrandAbstract;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

trait HasProductBrandUnidirectionalOneToOne
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
        $builder->addOwningOneToOne(
            ProductBrand::getSingular(),
            ProductBrand::class
        );
    }
}
