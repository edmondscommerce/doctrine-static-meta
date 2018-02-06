<?php
declare(strict_types=1);

namespace My\Test\Project\Entities\Product;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entities\Relations\Product\Interfaces\HasProduct;
use My\Test\Project\Entities\Relations\Product\Interfaces\ReciprocatesProduct;
use My\Test\Project\Entities\Relations\Product\Traits\HasProduct\HasProductInverseOneToOne;

class Brand implements DSM\Interfaces\UsesPHPMetaDataInterface, HasProduct, ReciprocatesProduct {

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\Fields\IdField;
	use HasProductInverseOneToOne;
}
