<?php
declare(strict_types=1);

namespace My\Test\Project\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\EntityRelations\Address\Interfaces\HasAddresses;
use My\Test\Project\EntityRelations\Address\Interfaces\ReciprocatesAddress;
use My\Test\Project\EntityRelations\Address\Traits\HasAddresses\HasAddressesOwningManyToMany;
use My\Test\Project\EntityRelations\Customer\Category\Interfaces\HasCategories;
use My\Test\Project\EntityRelations\Customer\Category\Interfaces\ReciprocatesCategory;
use My\Test\Project\EntityRelations\Customer\Category\Traits\HasCategories\HasCategoriesOwningManyToMany;
use My\Test\Project\EntityRelations\Customer\Segment\Interfaces\HasSegments;
use My\Test\Project\EntityRelations\Customer\Segment\Interfaces\ReciprocatesSegment;
use My\Test\Project\EntityRelations\Customer\Segment\Traits\HasSegments\HasSegmentsOwningManyToMany;
use My\Test\Project\EntityRelations\Order\Interfaces\HasOrders;
use My\Test\Project\EntityRelations\Order\Interfaces\ReciprocatesOrder;
use My\Test\Project\EntityRelations\Order\Traits\HasOrders\HasOrdersOneToMany;

class Customer implements 
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\Fields\IdFieldInterface,
    HasAddresses,
    ReciprocatesAddress,
    HasSegments,
    ReciprocatesSegment,
    HasCategories,
    ReciprocatesCategory,
    HasOrders,
    ReciprocatesOrder
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\Fields\IdFieldTrait;
	use HasAddressesOwningManyToMany;
	use HasSegmentsOwningManyToMany;
	use HasCategoriesOwningManyToMany;
	use HasOrdersOneToMany;
}
