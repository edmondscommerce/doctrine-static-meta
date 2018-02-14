<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer;

interface ReciprocatesCustomer
{
    public function reciprocateRelationOnCustomer(Customer $customer): UsesPHPMetaDataInterface;

    public function removeRelationOnCustomer(Customer $customer): UsesPHPMetaDataInterface;
}
