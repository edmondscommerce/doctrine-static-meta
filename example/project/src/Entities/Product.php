<?php
declare(strict_types=1);

namespace My\Test\Project\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\EntityRelations\Order\LineItem\Interfaces\HasLineItem;
use My\Test\Project\EntityRelations\Order\LineItem\Interfaces\ReciprocatesLineItem;
use My\Test\Project\EntityRelations\Order\LineItem\Traits\HasLineItem\HasLineItemInverseOneToOne;
use My\Test\Project\EntityRelations\Product\Brand\Interfaces\HasBrand;
use My\Test\Project\EntityRelations\Product\Brand\Interfaces\ReciprocatesBrand;
use My\Test\Project\EntityRelations\Product\Brand\Traits\HasBrand\HasBrandOwningOneToOne;

class Product implements 
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\Fields\IdFieldInterface,
    HasLineItem,
    ReciprocatesLineItem,
    HasBrand,
    ReciprocatesBrand
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\Fields\IdFieldTrait;
	use HasLineItemInverseOneToOne;
	use HasBrandOwningOneToOne;
}
