<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategories;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategoriesAbstract;
use My\Test\Project\Entities\Customer\Category;

/**
 * Trait HasCategoriesUnidirectionalOneToMany
 *
 * One instance of the current Entity (that is using this trait) has Many instances (references) to Category.
 *
 * @see     http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-unidirectional-with-join-table
 *
 * @package My\Test\Project\Entities\Traits\Relations\Category\HasCategories
 */
trait HasCategoriesUnidirectionalOneToMany
{
    use HasCategoriesAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForCategories(ClassMetadataBuilder $builder): void
    {
        $manyToManyBuilder = $builder->createManyToMany(
            Category::getPlural(),
            Category::class
        );
        $manyToManyBuilder->setJoinTable(static::getSingular().'_to_'.Category::getPlural());
        $manyToManyBuilder->addJoinColumn(
            static::getSingular().'_'.static::getIdField(),
            static::getIdField()
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Category::getSingular().'_'.Category::getIdField(),
            Category::getIdField()
        );
        $manyToManyBuilder->build();

    }
}
