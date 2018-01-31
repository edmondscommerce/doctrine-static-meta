<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer;

trait HasCustomersAbstract
{
    /**
     * @var ArrayCollection|Customer[]
     */
    private $customers;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForCustomers(ClassMetadataBuilder $builder);

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
            if (true === $recip) {
                $this->reciprocateRelationOnCustomer($customer, false);
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
        if (true === $recip) {
            $this->removeRelationOnCustomer($customer, false);
        }

        return $this;
    }

    private function initCustomers()
    {
        $this->customers = new ArrayCollection();

        return $this;
    }
}
