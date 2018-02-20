<?php
declare(strict_types=1);

namespace My\Test\Project\Entities\Order;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\EntityRelations\Address\Interfaces\HasAddress;
use My\Test\Project\EntityRelations\Address\Traits\HasAddress\HasAddressUnidirectionalOneToOne;
use My\Test\Project\EntityRelations\Order\Interfaces\HasOrder;
use My\Test\Project\EntityRelations\Order\Interfaces\ReciprocatesOrder;
use My\Test\Project\EntityRelations\Order\Traits\HasOrder\HasOrderManyToOne;

class Address implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasOrder,
    ReciprocatesOrder,
    HasAddress
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasOrderManyToOne;
    use HasAddressUnidirectionalOneToOne;
}
