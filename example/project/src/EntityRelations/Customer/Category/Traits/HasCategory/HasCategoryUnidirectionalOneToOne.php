<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategory;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategoryAbstract;
use My\Test\Project\Entities\Customer\Category;

trait HasCategoryUnidirectionalOneToOne
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
        $builder->addOwningOneToOne(
            Category::getSingular(),
            Category::class
        );
    }
}
