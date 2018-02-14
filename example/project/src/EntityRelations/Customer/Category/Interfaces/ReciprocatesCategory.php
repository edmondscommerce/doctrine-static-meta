<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Category\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category;

interface ReciprocatesCategory
{
    /**
     * @param Category $category
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnCategory(Category $category): UsesPHPMetaDataInterface;

    /**
     * @param Category $category
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnCategory(Category $category): UsesPHPMetaDataInterface;
}
