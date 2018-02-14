<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Product\Traits\HasProduct;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\EntityRelations\Product\Traits\ReciprocatesProduct;
use My\Test\Project\Entities\Product;
use  My\Test\Project\EntityRelations\Product\Traits\HasProductAbstract;

trait HasProductInverseOneToOne
{
    use HasProductAbstract;

    use ReciprocatesProduct;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForProduct(ClassMetadataBuilder $builder): void
    {
        $builder->addInverseOneToOne(
            Product::getSingular(),
            Product::class,
            static::getSingular()
        );
    }
}
