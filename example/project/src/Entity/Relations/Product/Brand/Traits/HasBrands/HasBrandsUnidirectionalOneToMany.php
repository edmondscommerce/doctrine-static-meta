<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Product\Brand\Traits\HasBrands;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entity\Relations\Product\Brand\Traits\HasBrandsAbstract;
use My\Test\Project\Entities\Product\Brand;

/**
 * Trait HasBrandsUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Brand.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\Brand\HasBrands
 */
trait HasBrandsUnidirectionalOneToMany
{
    use HasBrandsAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForBrands(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Brand::getPlural(),
            Brand::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.Brand::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Brand::getSingular().'_'.Brand::getIdField(),
            Brand::getIdField()
        );
        $manyToManyBuilder->build();

    }
}
