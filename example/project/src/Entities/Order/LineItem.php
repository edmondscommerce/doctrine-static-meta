<?php
declare(strict_types=1);

namespace My\Test\Project\Entities\Order;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entities\Relations\Order\Interfaces\HasOrder;
use My\Test\Project\Entities\Relations\Order\Interfaces\ReciprocatesOrder;
use My\Test\Project\Entities\Relations\Order\Traits\HasOrder\HasOrderManyToOne;
use My\Test\Project\Entities\Relations\Product\Interfaces\HasProduct;
use My\Test\Project\Entities\Relations\Product\Interfaces\ReciprocatesProduct;
use My\Test\Project\Entities\Relations\Product\Traits\HasProduct\HasProductOwningOneToOne;

class LineItem implements DSM\Interfaces\UsesPHPMetaDataInterface, HasOrder, ReciprocatesOrder, HasProduct, ReciprocatesProduct {

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\Fields\IdField;
	use HasOrderManyToOne;
	use HasProductOwningOneToOne;
}
