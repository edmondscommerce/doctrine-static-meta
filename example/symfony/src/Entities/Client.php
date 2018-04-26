<?php declare(strict_types=1);

namespace My\Test\Project\Entities;
// phpcs:disable

use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use My\Test\Project\Entity\Fields\Interfaces\NameFieldInterface;
use My\Test\Project\Entity\Fields\Traits\NameFieldTrait;
use My\Test\Project\Entity\Interfaces\ClientInterface;

// phpcs:enable
class Client implements 
    ClientInterface,
    NameFieldInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidatedEntityTrait;
	use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;
	use NameFieldTrait;

	public function __construct(EntityValidatorInterface $validator) {
		$this->setValidator($validator);
		$this->runInitMethods();
	}
}
