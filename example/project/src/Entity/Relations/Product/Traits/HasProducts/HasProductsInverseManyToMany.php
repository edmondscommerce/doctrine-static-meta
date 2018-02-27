<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Product\Traits\HasProducts;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Product\Traits\HasProductsAbstract;
use My\Test\Project\Entity\Relations\Product\Traits\ReciprocatesProduct;
use My\Test\Project\Entities\Product;

trait HasProductsInverseManyToMany
{
    use HasProductsAbstract;

    use ReciprocatesProduct;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForProducts(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Product::getPlural(),
            Product::class
        );
        $manyToManyBuilder->mappedBy(static::getPlural());
        $manyToManyBuilder->setJoinTable(Product::getPlural().'_to_'.static::getPlural());
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
