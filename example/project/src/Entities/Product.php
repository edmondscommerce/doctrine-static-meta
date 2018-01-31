<?php
declare(strict_types=1);

namespace My\Test\Project\Entities;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entities\Relations\Order\LineItem\Interfaces\HasLineItem;
use My\Test\Project\Entities\Relations\Order\LineItem\Interfaces\ReciprocatesLineItem;
use My\Test\Project\Entities\Relations\Order\LineItem\Traits\HasLineItem\HasLineItemInverseOneToOne;
use My\Test\Project\Entities\Relations\Product\Brand\Interfaces\HasBrand;
use My\Test\Project\Entities\Relations\Product\Brand\Interfaces\ReciprocatesBrand;
use My\Test\Project\Entities\Relations\Product\Brand\Traits\HasBrand\HasBrandOwningOneToOne;

class Product implements DSM\Interfaces\UsesPHPMetaDataInterface, HasLineItem, ReciprocatesLineItem, HasBrand, ReciprocatesBrand {

	use DSM\Traits\UsesPHPMetaData;
	use DSM\Traits\Fields\IdField;
	use HasLineItemInverseOneToOne;
	use HasBrandOwningOneToOne;
}
