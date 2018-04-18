<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Category\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer\Category as CustomerCategory;
use  My\Test\Project\Entity\Relations\Customer\Category\Interfaces\HasCustomerCategoryInterface;
use  My\Test\Project\Entity\Relations\Customer\Category\Interfaces\ReciprocatesCustomerCategoryInterface;


trait HasCustomerCategoryAbstract
{
    /**
     * @var CustomerCategory|null
     */
    private $customerCategory;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForCustomerCategory(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForCustomerCategory(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasCustomerCategoryInterface::PROPERTY_NAME_CUSTOMER_CATEGORY,
            new Valid()
        );
    }

    /**
     * @return CustomerCategory|null
     */
    public function getCustomerCategory(): ?CustomerCategory
    {
        return $this->customerCategory;
    }

    /**
     * @param CustomerCategory $customerCategory
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCustomerCategory(
        CustomerCategory $customerCategory,
        bool $recip = true
    ): self {
        if ($this instanceof ReciprocatesCustomerCategoryInterface && true === $recip) {
            $this->reciprocateRelationOnCustomerCategory($customerCategory);
        }
        $this->customerCategory = $customerCategory;

        return $this;
    }

    /**
     * @return self
     */
    public function removeCustomerCategory(): self
    {
        $this->customerCategory = null;

        return $this;
    }
}
