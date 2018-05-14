<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\HasCustomerCategoriesInterface;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\ReciprocatesCustomerCategoryInterface;

trait HasCustomerCategoriesAbstract
{
    /**
     * @var ArrayCollection|CustomerCategory[]
     */
    private $customerCategories;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForCustomerCategories(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasCustomerCategoriesInterface::PROPERTY_NAME_CUSTOMER_CATEGORIES,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForCustomerCategories(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|CustomerCategory[]
     */
    public function getCustomerCategories(): Collection
    {
        return $this->customerCategories;
    }

    /**
     * @param Collection|CustomerCategory[] $customerCategories
     *
     * @return self
     */
    public function setCustomerCategories(Collection $customerCategories): HasCustomerCategoriesInterface
    {
        $this->customerCategories = $customerCategories;

        return $this;
    }

    /**
     * @param CustomerCategory|null $customerCategory
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCustomerCategory(
        ?CustomerCategory $customerCategory,
        bool $recip = true
    ): HasCustomerCategoriesInterface {
        if ($customerCategory === null) {
            return $this;
        }

        if (!$this->customerCategories->contains($customerCategory)) {
            $this->customerCategories->add($customerCategory);
            if ($this instanceof ReciprocatesCustomerCategoryInterface && true === $recip) {
                $this->reciprocateRelationOnCustomerCategory($customerCategory);
            }
        }

        return $this;
    }

    /**
     * @param CustomerCategory $customerCategory
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCustomerCategory(
        CustomerCategory $customerCategory,
        bool $recip = true
    ): HasCustomerCategoriesInterface {
        $this->customerCategories->removeElement($customerCategory);
        if ($this instanceof ReciprocatesCustomerCategoryInterface && true === $recip) {
            $this->removeRelationOnCustomerCategory($customerCategory);
        }

        return $this;
    }

    /**
     * Initialise the customerCategories property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initCustomerCategories()
    {
        $this->customerCategories = new ArrayCollection();

        return $this;
    }
}
