<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Category\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\Entities\Relations\Customer\Category\Interfaces\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;

trait HasCategoryAbstract
{
    /**
     * @var Category|null
     */
    private $category = null;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForCategory(ClassMetadataBuilder $builder);

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesCategory && true === $recip) {
            $this->reciprocateRelationOnCategory($category);
        }
        $this->category = $category;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeCategory(): UsesPHPMetaDataInterface
    {
        $this->category = null;

        return $this;
    }
}
