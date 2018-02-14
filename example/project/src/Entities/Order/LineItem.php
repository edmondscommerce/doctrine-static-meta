<?php
declare(strict_types=1);

namespace My\Test\Project\Entities\Order;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\EntityRelations\Order\Interfaces\HasOrder;
use My\Test\Project\EntityRelations\Order\Interfaces\ReciprocatesOrder;
use My\Test\Project\EntityRelations\Order\Traits\HasOrder\HasOrderManyToOne;
use My\Test\Project\EntityRelations\Product\Interfaces\HasProduct;
use My\Test\Project\EntityRelations\Product\Interfaces\ReciprocatesProduct;
use My\Test\Project\EntityRelations\Product\Traits\HasProduct\HasProductOwningOneToOne;

class LineItem implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\Fields\IdFieldInterface,
    HasOrder,
    ReciprocatesOrder,
    HasProduct,
    ReciprocatesProduct
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Traits\Fields\IdFieldTrait;
    use HasOrderManyToOne;
    use HasProductOwningOneToOne;
}
