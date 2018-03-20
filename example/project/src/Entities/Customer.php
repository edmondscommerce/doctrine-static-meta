<?php declare(strict_types=1);

namespace My\Test\Project\Entities;
// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Interfaces\CustomerInterface;
use My\Test\Project\Entity\Relations\Address\Interfaces\HasAddressesInterface;
use My\Test\Project\Entity\Relations\Address\Interfaces\ReciprocatesAddressInterface;
use My\Test\Project\Entity\Relations\Address\Traits\HasAddresses\HasAddressesOwningManyToMany;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\HasCustomerCategoriesInterface;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\ReciprocatesCustomerCategoryInterface;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCustomerCategories\HasCustomerCategoriesOwningManyToMany;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\HasCustomerSegmentsInterface;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\ReciprocatesCustomerSegmentInterface;
use My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegments\HasCustomerSegmentsOwningManyToMany;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrdersInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrders\HasOrdersOneToMany;

// phpcs:enable
class Customer implements 
    CustomerInterface,
    HasAddressesInterface,
    ReciprocatesAddressInterface,
    HasCustomerSegmentsInterface,
    ReciprocatesCustomerSegmentInterface,
    HasCustomerCategoriesInterface,
    ReciprocatesCustomerCategoryInterface,
    HasOrdersInterface,
    ReciprocatesOrderInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidateTrait;
	use DSM\Fields\Traits\IdFieldTrait;
	use HasAddressesOwningManyToMany;
	use HasCustomerSegmentsOwningManyToMany;
	use HasCustomerCategoriesOwningManyToMany;
	use HasOrdersOneToMany;
}
