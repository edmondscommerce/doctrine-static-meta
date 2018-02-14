<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Category\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category;

interface HasCategories
{
    public static function getPropertyMetaForCategories(ClassMetadataBuilder $builder);

    public function getCategories(): Collection;

    public function setCategories(Collection $categories): UsesPHPMetaDataInterface;

    public function addCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface;

}
