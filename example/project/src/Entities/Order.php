<?php declare(strict_types=1);

namespace My\Test\Project\Entities;
// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Interfaces\OrderInterface;
use My\Test\Project\Entity\Relations\Customer\Interfaces\HasCustomerInterface;
use My\Test\Project\Entity\Relations\Customer\Interfaces\ReciprocatesCustomerInterface;
use My\Test\Project\Entity\Relations\Customer\Traits\HasCustomer\HasCustomerManyToOne;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\HasOrderAddressesInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddresses\HasOrderAddressesOneToMany;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\HasOrderLineItemsInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\ReciprocatesOrderLineItemInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Traits\HasOrderLineItems\HasOrderLineItemsOneToMany;

// phpcs:enable
class Order implements 
    OrderInterface,
    HasCustomerInterface,
    ReciprocatesCustomerInterface,
    HasOrderAddressesInterface,
    ReciprocatesOrderAddressInterface,
    HasOrderLineItemsInterface,
    ReciprocatesOrderLineItemInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidateTrait;
	use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
	use HasCustomerManyToOne;
	use HasOrderAddressesOneToMany;
	use HasOrderLineItemsOneToMany;
}
