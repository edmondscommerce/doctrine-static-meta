<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Category\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\Entities\Relations\Customer\Category\Interfaces\ReciprocatesCategory;
use My\Test\Project\Entities\Customer\Category;

trait HasCategoriesAbstract
{
    /**
     * @var ArrayCollection|Category[]
     */
    private $categories;

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForCategories(ClassMetadataBuilder $manyToManyBuilder): void;

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection $categories
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setCategories(Collection $categories): UsesPHPMetaDataInterface
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @param Category $category
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function addCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            if ($this instanceof ReciprocatesCategory && true === $recip) {
                $this->reciprocateRelationOnCategory($category);
            }
        }

        return $this;
    }

    /**
     * @param Category $category
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->categories->removeElement($category);
        if ($this instanceof ReciprocatesCategory && true === $recip) {
            $this->removeRelationOnCategory($category);
        }

        return $this;
    }

    /**
     * Initialise the categories property as a Doctrine ArrayCollection
     *
     * @return $this
     */
    private function initCategories()
    {
        $this->categories = new ArrayCollection();

        return $this;
    }
}
