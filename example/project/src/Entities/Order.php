<?php declare(strict_types=1);

namespace My\Test\Project\Entities;
// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Relations\Customer\Interfaces\HasCustomerInterface;
use My\Test\Project\Entity\Relations\Customer\Interfaces\ReciprocatesCustomerInterface;
use My\Test\Project\Entity\Relations\Customer\Traits\HasCustomer\HasCustomerManyToOne;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\HasAddressesInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesAddressInterface;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasAddresses\HasAddressesOneToMany;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\HasLineItemsInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\ReciprocatesLineItemInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasLineItems\HasLineItemsOneToMany;

// phpcs:enable
class Order implements 
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasCustomerInterface,
    ReciprocatesCustomerInterface,
    HasAddressesInterface,
    ReciprocatesAddressInterface,
    HasLineItemsInterface,
    ReciprocatesLineItemInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidateTrait;
	use DSM\Fields\Traits\IdFieldTrait;
	use HasCustomerManyToOne;
	use HasAddressesOneToMany;
	use HasLineItemsOneToMany;
}
