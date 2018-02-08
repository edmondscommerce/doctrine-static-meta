<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\Entities\Relations\Customer\Interfaces\ReciprocatesCustomer;
use My\Test\Project\Entities\Customer;

trait HasCustomersAbstract
{
    /**
     * @var ArrayCollection|Customer[]
     */
    private $customers;

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForCustomers(ClassMetadataBuilder $manyToManyBuilder): void;

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    /**
     * @param Collection $customers
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
     */
    public function addCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            if ($this instanceof ReciprocatesCustomer && true === $recip) {
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
     */
    public function removeCustomer(Customer $customer, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->customers->removeElement($customer);
        if ($this instanceof ReciprocatesCustomer && true === $recip) {
            $this->removeRelationOnCustomer($customer);
        }

        return $this;
    }

    /**
     * Initialise the customers property as a Doctrine ArrayCollection
     *
     * @return $this
     */
    private function initCustomers()
    {
        $this->customers = new ArrayCollection();

        return $this;
    }
}
