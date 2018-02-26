<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer\Category;
use  My\Test\Project\Entity\Relations\Customer\Category\Interfaces\HasCategoryInterface;
use  My\Test\Project\Entity\Relations\Customer\Category\Interfaces\ReciprocatesCategoryInterface;


trait HasCategoryAbstract
{
    /**
     * @var Category|null
     */
    private $category;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForCategory(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForCategories(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasCategoryInterface::PROPERTY_NAME_CATEGORY, new Valid());
    }

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
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCategory(Category $category, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesCategoryInterface && true === $recip) {
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
