<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer\Category;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\HasCategoriesInterface;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\ReciprocatesCategoryInterface;

trait HasCategoriesAbstract
{
    /**
     * @var ArrayCollection|Category[]
     */
    private $categories;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForCategories(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasCategoriesInterface::PROPERTY_NAME_CATEGORIES, new Valid());
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForCategories(ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection|Category[] $categories
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
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            if ($this instanceof ReciprocatesCategoryInterface && true === $recip) {
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
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->categories->removeElement($category);
        if ($this instanceof ReciprocatesCategoryInterface && true === $recip) {
            $this->removeRelationOnCategory($category);
        }

        return $this;
    }

    /**
     * Initialise the categories property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initCategories()
    {
        $this->categories = new ArrayCollection();

        return $this;
    }
}
