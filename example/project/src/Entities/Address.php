<?php
declare(strict_types=1);

namespace My\Test\Project\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entities\Relations\Customer\Interfaces\HasCustomers;
use My\Test\Project\Entities\Relations\Customer\Interfaces\ReciprocatesCustomer;
use My\Test\Project\Entities\Relations\Customer\Traits\HasCustomers\HasCustomersInverseManyToMany;

class Address implements DSM\Interfaces\UsesPHPMetaDataInterface, DSM\Interfaces\Fields\IdFieldInterface, HasCustomers, ReciprocatesCustomer {

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\Fields\IdFieldTrait;
	use HasCustomersInverseManyToMany;
}
