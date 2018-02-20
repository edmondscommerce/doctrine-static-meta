<?php
declare(strict_types=1);

namespace My\Test\Project\Entities\Customer;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\EntityRelations\Customer\Interfaces\HasCustomers;
use My\Test\Project\EntityRelations\Customer\Interfaces\ReciprocatesCustomer;
use My\Test\Project\EntityRelations\Customer\Traits\HasCustomers\HasCustomersInverseManyToMany;

class Category implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasCustomers,
    ReciprocatesCustomer
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasCustomersInverseManyToMany;
}
