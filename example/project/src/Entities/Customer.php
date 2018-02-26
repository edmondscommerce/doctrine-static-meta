<?php declare(strict_types=1);

namespace My\Test\Project\Entities;
// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Relations\Address\Interfaces\HasAddressesInterface;
use My\Test\Project\Entity\Relations\Address\Interfaces\ReciprocatesAddressInterface;
use My\Test\Project\Entity\Relations\Address\Traits\HasAddresses\HasAddressesOwningManyToMany;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\HasCategoriesInterface;
use My\Test\Project\Entity\Relations\Customer\Category\Interfaces\ReciprocatesCategoryInterface;
use My\Test\Project\Entity\Relations\Customer\Category\Traits\HasCategories\HasCategoriesOwningManyToMany;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\HasSegmentsInterface;
use My\Test\Project\Entity\Relations\Customer\Segment\Interfaces\ReciprocatesSegmentInterface;
use My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasSegments\HasSegmentsOwningManyToMany;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrdersInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrders\HasOrdersOneToMany;

// phpcs:enable
class Customer implements 
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasAddressesInterface,
    ReciprocatesAddressInterface,
    HasSegmentsInterface,
    ReciprocatesSegmentInterface,
    HasCategoriesInterface,
    ReciprocatesCategoryInterface,
    HasOrdersInterface,
    ReciprocatesOrderInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidateTrait;
	use DSM\Fields\Traits\IdFieldTrait;
	use HasAddressesOwningManyToMany;
	use HasSegmentsOwningManyToMany;
	use HasCategoriesOwningManyToMany;
	use HasOrdersOneToMany;
}
