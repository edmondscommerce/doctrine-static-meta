<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Order;
// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use My\Test\Project\Entity\Fields\Traits\BooleanFieldTrait;
use My\Test\Project\Entity\Fields\Traits\DatetimeFieldTrait;
use My\Test\Project\Entity\Fields\Traits\DecimalFieldTrait;
use My\Test\Project\Entity\Fields\Traits\FloatFieldTrait;
use My\Test\Project\Entity\Fields\Traits\IntegerFieldTrait;
use My\Test\Project\Entity\Fields\Traits\JsonFieldTrait;
use My\Test\Project\Entity\Fields\Traits\StringFieldTrait;
use My\Test\Project\Entity\Fields\Traits\TextFieldTrait;
use My\Test\Project\Entity\Interfaces\Order\AddressInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddress\HasAttributesAddressUnidirectionalOneToOne;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelation\HasLargeRelationManyToOne;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrder\HasOrderManyToOne;
use My\Test\Project\Entity\Repositories\Order\AddressRepository;

// phpcs:enable
class Address implements 
    AddressInterface
{

	use DSM\Traits\UsesPHPMetaDataTrait;
	use DSM\Traits\ValidatedEntityTrait;
	use DSM\Traits\ImplementNotifyChangeTrackingPolicy;
	use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;
	use StringFieldTrait;
	use DatetimeFieldTrait;
	use FloatFieldTrait;
	use DecimalFieldTrait;
	use IntegerFieldTrait;
	use TextFieldTrait;
	use BooleanFieldTrait;
	use JsonFieldTrait;
	use HasOrderManyToOne;
	use HasAttributesAddressUnidirectionalOneToOne;
	use HasLargeRelationManyToOne;

	/**
	 * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @param ClassMetadataBuilder $builder
	 */
	private static function setCustomRepositoryClass(ClassMetadataBuilder $builder) {
		$builder->setCustomRepositoryClass(AddressRepository::class);
	}

	public function __construct() {
		$this->runInitMethods();
	}
}
