<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Product\Traits\HasProduct;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Product\Traits\HasProductAbstract;
use My\Test\Project\Entities\Product;

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
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Product::getSingular(),
            Product::class
        );
    }
}
