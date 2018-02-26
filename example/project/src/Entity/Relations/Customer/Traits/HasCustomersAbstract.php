<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Customer;
use  My\Test\Project\Entity\Relations\Customer\Interfaces\HasCustomersInterface;
use  My\Test\Project\Entity\Relations\Customer\Interfaces\ReciprocatesCustomerInterface;

trait HasCustomersAbstract
{
    /**
     * @var ArrayCollection|Customer[]
     */
    private $customers;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForCustomers(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasCustomersInterface::PROPERTY_NAME_CUSTOMERS, new Valid());
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForCustomers(ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    /**
     * @param Collection|Customer[] $customers
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setCustomers(Collection $customers): UsesPHPMetaDataInterface
    {
        $this->customers = $customers;

        return $this;
    }

    /**
     * @param Customer $customer
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            if ($this instanceof ReciprocatesCustomerInterface && true === $recip) {
                $this->reciprocateRelationOnCustomer($customer);
            }
        }

        return $this;
    }

    /**
     * @param Customer $customer
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->customers->removeElement($customer);
        if ($this instanceof ReciprocatesCustomerInterface && true === $recip) {
            $this->removeRelationOnCustomer($customer);
        }

        return $this;
    }

    /**
     * Initialise the customers property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initCustomers()
    {
        $this->customers = new ArrayCollection();

        return $this;
    }
}
