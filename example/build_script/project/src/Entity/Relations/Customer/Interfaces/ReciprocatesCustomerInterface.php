<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer as Customer;

interface ReciprocatesCustomerInterface
{
    /**
     * @param Customer $customer
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnCustomer(
        Customer $customer
    ): UsesPHPMetaDataInterface;

    /**
     * @param Customer $customer
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnCustomer(
        Customer $customer
    ): UsesPHPMetaDataInterface;
}
