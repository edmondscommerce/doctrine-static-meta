<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Traits;

use My\Test\Project\Entities\Customer as Customer;
use My\Test\Project\Entity\Relations\Customer\Interfaces\ReciprocatesCustomerInterface;

trait ReciprocatesCustomer
{
    /**
     * This method needs to set the relationship on the customer to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Customer|null $customer
     *
     * @return ReciprocatesCustomerInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnCustomer(
        Customer $customer
    ): ReciprocatesCustomerInterface {
        $singular = static::getSingular();
        $method   = 'add'.$singular;
        if (!method_exists($customer, $method)) {
            $method = 'set'.$singular;
        }

        $customer->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the customer to this entity.
     *
     * @param Customer $customer
     *
     * @return ReciprocatesCustomerInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnCustomer(
        Customer $customer
    ): ReciprocatesCustomerInterface {
        $method = 'remove'.static::getSingular();
        $customer->$method($this, false);

        return $this;
    }
}
