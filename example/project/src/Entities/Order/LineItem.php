<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Order;
// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use My\Test\Project\Entity\Interfaces\Order\LineItemInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrder\HasOrderManyToOne;
use My\Test\Project\Entity\Relations\Product\Interfaces\HasProductInterface;
use My\Test\Project\Entity\Relations\Product\Interfaces\ReciprocatesProductInterface;
use My\Test\Project\Entity\Relations\Product\Traits\HasProduct\HasProductOwningOneToOne;

// phpcs:enable
class LineItem implements 
    LineItemInterface,
    HasOrderInterface,
    ReciprocatesOrderInterface,
    HasProductInterface,
    ReciprocatesProductInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidatedEntityTrait;
	use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
	use HasOrderManyToOne;
	use HasProductOwningOneToOne;

	public function __construct(EntityValidatorInterface $validator) {
		$this->injectValidator($validator);
		$this->runInitMethods();
	}
}
