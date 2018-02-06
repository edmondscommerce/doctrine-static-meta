<?php
declare(strict_types=1);

namespace My\Test\Project\Entities\Customer;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entities\Relations\Customer\Interfaces\HasCustomers;
use My\Test\Project\Entities\Relations\Customer\Interfaces\ReciprocatesCustomer;
use My\Test\Project\Entities\Relations\Customer\Traits\HasCustomers\HasCustomersInverseManyToMany;

class Segment implements DSM\Interfaces\UsesPHPMetaDataInterface, HasCustomers, ReciprocatesCustomer {

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\Fields\IdField;
	use HasCustomersInverseManyToMany;
}
