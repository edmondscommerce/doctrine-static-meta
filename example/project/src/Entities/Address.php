<?php declare(strict_types=1);

namespace My\Test\Project\Entities;

// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Interfaces\AddressInterface;
use My\Test\Project\Entity\Relations\Customer\Interfaces\HasCustomersInterface;
use My\Test\Project\Entity\Relations\Customer\Interfaces\ReciprocatesCustomerInterface;
use My\Test\Project\Entity\Relations\Customer\Traits\HasCustomers\HasCustomersInverseManyToMany;

// phpcs:enable
class Address implements
    AddressInterface,
    HasCustomersInterface,
    ReciprocatesCustomerInterface
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\ValidateTrait;
    use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
    use HasCustomersInverseManyToMany;
}
