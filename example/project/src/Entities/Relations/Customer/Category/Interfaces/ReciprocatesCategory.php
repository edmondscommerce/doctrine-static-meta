<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Category\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category;

interface ReciprocatesCategory
{
    public function reciprocateRelationOnCategory(Category $category): UsesPHPMetaDataInterface;

    public function removeRelationOnCategory(Category $category): UsesPHPMetaDataInterface;
}
