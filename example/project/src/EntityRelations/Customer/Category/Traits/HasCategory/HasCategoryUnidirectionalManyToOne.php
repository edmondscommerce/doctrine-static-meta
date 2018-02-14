<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategory;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategoryAbstract;
use My\Test\Project\Entities\Customer\Category;

/**
 * Trait HasCategoryManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Category\HasCategory
 */
trait HasCategoryUnidirectionalManyToOne
{
    use HasCategoryAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyMetaForCategory(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Category::getSingular(),
            Category::class
        );
    }
}
