<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Order;
// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use My\Test\Project\Entity\Interfaces\Order\AddressInterface;
use My\Test\Project\Entity\Relations\Address\Interfaces\HasAddressInterface;
use My\Test\Project\Entity\Relations\Address\Traits\HasAddress\HasAddressUnidirectionalOneToOne;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrder\HasOrderManyToOne;

// phpcs:enable
class Address implements 
    AddressInterface,
    HasOrderInterface,
    ReciprocatesOrderInterface,
    HasAddressInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidatedEntityTrait;
	use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
	use HasOrderManyToOne;
	use HasAddressUnidirectionalOneToOne;

	public function __construct(EntityValidatorInterface $validator) {
		$this->setValidator($validator);
		$this->runInitMethods();
	}
}
