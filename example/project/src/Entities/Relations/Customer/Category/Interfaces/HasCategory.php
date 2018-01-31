<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Category\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category;

interface HasCategory
{
    static function getPropertyMetaForCategory(ClassMetadataBuilder $builder);

    public function getCategory(): ?Category;

    public function setCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeCategory(): UsesPHPMetaDataInterface;

}
