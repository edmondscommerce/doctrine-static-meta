<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer;
use  My\Test\Project\Entity\Relations\Customer\Interfaces\HasCustomerInterface;
use  My\Test\Project\Entity\Relations\Customer\Interfaces\ReciprocatesCustomerInterface;


trait HasCustomerAbstract
{
    /**
     * @var Customer|null
     */
    private $customer;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForCustomer(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForCustomers(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasCustomerInterface::PROPERTY_NAME_CUSTOMER, new Valid());
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesCustomerInterface && true === $recip) {
            $this->reciprocateRelationOnCustomer($customer);
        }
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeCustomer(): UsesPHPMetaDataInterface
    {
        $this->customer = null;

        return $this;
    }
}
