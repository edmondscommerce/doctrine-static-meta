<?php
declare(strict_types=1);

namespace My\Test\Project\Entities\Product;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\EntityRelations\Product\Interfaces\HasProduct;
use My\Test\Project\EntityRelations\Product\Interfaces\ReciprocatesProduct;
use My\Test\Project\EntityRelations\Product\Traits\HasProduct\HasProductInverseOneToOne;

class Brand implements
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasProduct,
    ReciprocatesProduct
{

    use DSM\Traits\UsesPHPMetaDataTrait;
    use DSM\Fields\Traits\IdFieldTrait;
    use HasProductInverseOneToOne;
}
