<?php
declare(strict_types=1);

namespace My\Test\Project\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entities\Relations\Customer\Interfaces\HasCustomer;
use My\Test\Project\Entities\Relations\Customer\Interfaces\ReciprocatesCustomer;
use My\Test\Project\Entities\Relations\Customer\Traits\HasCustomer\HasCustomerManyToOne;
use My\Test\Project\Entities\Relations\Order\Address\Interfaces\HasAddresses;
use My\Test\Project\Entities\Relations\Order\Address\Interfaces\ReciprocatesAddress;
use My\Test\Project\Entities\Relations\Order\Address\Traits\HasAddresses\HasAddressesOneToMany;
use My\Test\Project\Entities\Relations\Order\LineItem\Interfaces\HasLineItems;
use My\Test\Project\Entities\Relations\Order\LineItem\Interfaces\ReciprocatesLineItem;
use My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItems\HasLineItemsOneToMany;

class Order implements DSM\Interfaces\UsesPHPMetaDataInterface, HasCustomer, ReciprocatesCustomer, HasAddresses, ReciprocatesAddress, HasLineItems, ReciprocatesLineItem {

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\Fields\IdField;
	use HasCustomerManyToOne;
	use HasAddressesOneToMany;
	use HasLineItemsOneToMany;
}
