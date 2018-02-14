<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Category\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category;

interface HasCategories
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForCategories(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection;

    /**
     * @param Collection|Category[] $categories
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setCategories(Collection $categories): UsesPHPMetaDataInterface;

    /**
     * @param Category $category
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @param Category $category
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface;
}
