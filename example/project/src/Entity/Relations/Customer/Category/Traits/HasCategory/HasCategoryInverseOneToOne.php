<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCategory;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCategoryAbstract;

trait HasCategoryInverseOneToOne
{
    use HasCategoryAbstract;

    use ReciprocatesCategory;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCategory(ClassMetadataBuilder $builder): void
    {
        $builder->addInverseOneToOne(
            Category::getSingular(),
            Category::class,
            static::getSingular()
        );
    }
}
