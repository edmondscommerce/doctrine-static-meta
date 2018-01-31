<?php
declare(strict_types=1);

namespace My\Test\Project\Entities\Order;

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entities\Relations\Address\Interfaces\HasAddress;
use My\Test\Project\Entities\Relations\Address\Traits\HasAddress\HasAddressUnidirectionalOneToOne;
use My\Test\Project\Entities\Relations\Order\Interfaces\HasOrder;
use My\Test\Project\Entities\Relations\Order\Interfaces\ReciprocatesOrder;
use My\Test\Project\Entities\Relations\Order\Traits\HasOrder\HasOrderManyToOne;

class Address implements DSM\Interfaces\UsesPHPMetaDataInterface, HasOrder, ReciprocatesOrder, HasAddress {

	use DSM\Traits\UsesPHPMetaData;
	use DSM\Traits\Fields\IdField;
	use HasOrderManyToOne;
	use HasAddressUnidirectionalOneToOne;
}
