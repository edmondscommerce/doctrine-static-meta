<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Category\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Category;

interface HasCategory
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForCategory(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Category
     */
    public function getCategory(): ?Category;

    /**
     * @param Category $category
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeCategory(): UsesPHPMetaDataInterface;
}
