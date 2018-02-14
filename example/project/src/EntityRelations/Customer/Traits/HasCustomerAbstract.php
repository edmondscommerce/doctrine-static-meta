<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\EntityRelations\Customer\Interfaces\ReciprocatesCustomer;
use My\Test\Project\Entities\Customer;

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
    abstract public static function getPropertyMetaForCustomer(ClassMetadataBuilder $builder): void;

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
        if ($this instanceof ReciprocatesCustomer && true === $recip) {
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
