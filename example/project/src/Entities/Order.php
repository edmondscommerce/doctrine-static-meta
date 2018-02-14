<?php
declare(strict_types=1);

namespace My\Test\Project\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\EntityRelations\Customer\Interfaces\HasCustomer;
use My\Test\Project\EntityRelations\Customer\Interfaces\ReciprocatesCustomer;
use My\Test\Project\EntityRelations\Customer\Traits\HasCustomer\HasCustomerManyToOne;
use My\Test\Project\EntityRelations\Order\Address\Interfaces\HasAddresses;
use My\Test\Project\EntityRelations\Order\Address\Interfaces\ReciprocatesAddress;
use My\Test\Project\EntityRelations\Order\Address\Traits\HasAddresses\HasAddressesOneToMany;
use My\Test\Project\EntityRelations\Order\LineItem\Interfaces\HasLineItems;
use My\Test\Project\EntityRelations\Order\LineItem\Interfaces\ReciprocatesLineItem;
use My\Test\Project\EntityRelations\Order\LineItem\Traits\HasLineItems\HasLineItemsOneToMany;

class Order implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\Fields\IdFieldInterface,
    HasCustomer,
    ReciprocatesCustomer,
    HasAddresses,
    ReciprocatesAddress,
    HasLineItems,
    ReciprocatesLineItem
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\Fields\IdFieldTrait;
    use HasCustomerManyToOne;
    use HasAddressesOneToMany;
    use HasLineItemsOneToMany;
}
