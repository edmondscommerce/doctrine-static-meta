<?php
declare(strict_types=1);

namespace My\Test\Project\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entities\Relations\Address\Interfaces\HasAddresses;
use My\Test\Project\Entities\Relations\Address\Interfaces\ReciprocatesAddress;
use My\Test\Project\Entities\Relations\Address\Traits\HasAddresses\HasAddressesOwningManyToMany;
use My\Test\Project\Entities\Relations\Customer\Category\Interfaces\HasCategories;
use My\Test\Project\Entities\Relations\Customer\Category\Interfaces\ReciprocatesCategory;
use My\Test\Project\Entities\Relations\Customer\Category\Traits\HasCategories\HasCategoriesOwningManyToMany;
use My\Test\Project\Entities\Relations\Customer\Segment\Interfaces\HasSegments;
use My\Test\Project\Entities\Relations\Customer\Segment\Interfaces\ReciprocatesSegment;
use My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegments\HasSegmentsOwningManyToMany;
use My\Test\Project\Entities\Relations\Order\Interfaces\HasOrders;
use My\Test\Project\Entities\Relations\Order\Interfaces\ReciprocatesOrder;
use My\Test\Project\Entities\Relations\Order\Traits\HasOrders\HasOrdersOneToMany;

class Customer implements DSM\Interfaces\UsesPHPMetaDataInterface, HasAddresses, ReciprocatesAddress, HasSegments, ReciprocatesSegment, HasCategories, ReciprocatesCategory, HasOrders, ReciprocatesOrder {

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\Fields\IdField;
	use HasAddressesOwningManyToMany;
	use HasSegmentsOwningManyToMany;
	use HasCategoriesOwningManyToMany;
	use HasOrdersOneToMany;
}
