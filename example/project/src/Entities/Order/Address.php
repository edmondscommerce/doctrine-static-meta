<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Order;
// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Relations\Address\Interfaces\HasAddressInterface;
use My\Test\Project\Entity\Relations\Address\Traits\HasAddress\HasAddressUnidirectionalOneToOne;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrder\HasOrderManyToOne;

// phpcs:enable
class Address implements 
    DSM\Interfaces\UsesPHPMetaDataInterface,
    DSM\Interfaces\ValidateInterface,
    DSM\Fields\Interfaces\IdFieldInterface,
    HasOrderInterface,
    ReciprocatesOrderInterface,
    HasAddressInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidateTrait;
	use DSM\Fields\Traits\IdFieldTrait;
	use HasOrderManyToOne;
	use HasAddressUnidirectionalOneToOne;
}
