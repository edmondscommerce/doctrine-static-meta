<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Product\Traits\HasProduct;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Product\Traits\HasProductAbstract;
use My\Test\Project\Entities\Product;

trait HasProductUnidirectionalOneToOne
{
    use HasProductAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Product::getSingular(),
            Product::class
        );
    }
}
