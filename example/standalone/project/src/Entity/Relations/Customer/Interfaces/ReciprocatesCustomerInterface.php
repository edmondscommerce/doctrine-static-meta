<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Interfaces;

use My\Test\Project\Entities\Customer as Customer;

interface ReciprocatesCustomerInterface
{
    /**
     * @param Customer $customer
     *
     * @return self
     */
    public function reciprocateRelationOnCustomer(
        Customer $customer
    ): self;

    /**
     * @param Customer $customer
     *
     * @return self
     */
    public function removeRelationOnCustomer(
        Customer $customer
    ): self;
}
