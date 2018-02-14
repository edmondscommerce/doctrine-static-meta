<?php declare(strict_types=1);


namespace My\Test\Project\EntityRelations\Product\Traits\HasProducts;


use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\EntityRelations\Product\Traits\HasProductsAbstract;
use  My\Test\Project\EntityRelations\Product\Traits\ReciprocatesProduct;
use My\Test\Project\Entities\Product;

trait HasProductsOwningManyToMany
{
    use HasProductsAbstract;

    use ReciprocatesProduct;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForProducts(ClassMetadataBuilder $builder): void
    {

        $manyToManyBuilder = $builder->createManyToMany(
            Product::getPlural(), Product::class
        );
        $manyToManyBuilder->inversedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(static::getPlural().'_to_'.Product::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Product::getSingular().'_'.Product::getIdField(),
            Product::getIdField()
        );
        $manyToManyBuilder->build();
    }
}
