<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Traits\HasProduct;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Product\Traits\HasProductAbstract;
use My\Test\Project\Entities\Product;

trait HasProductUnidirectionalOneToOne
{
    use HasProductAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Product::getSingular(),
            Product::class
        );
    }
}
