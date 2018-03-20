<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrands;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\HasProductBrandsAbstract;
use My\Test\Project\Entities\Product\Brand as ProductBrand;

/**
 * Trait HasProductBrandsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to ProductBrand.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\ProductBrand\HasProductBrands
 */
trait HasProductBrandsUnidirectionalOneToMany
{
    use HasProductBrandsAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForProductBrands(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            ProductBrand::getPlural(),
            ProductBrand::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.ProductBrand::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            ProductBrand::getSingular().'_'.ProductBrand::getIdField(),
            ProductBrand::getIdField()
        );
        $manyToManyBuilder->build();

    }
}
