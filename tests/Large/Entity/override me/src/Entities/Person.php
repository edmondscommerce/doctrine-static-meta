<?php declare(strict_types=1);

namespace My\Test\Project\Entities;
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
use My\Test\Project\Entity\Interfaces\PersonInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddress\HasAttributesAddressUnidirectionalManyToOne;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmails\HasAttributesEmailsOneToMany;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirector\HasCompanyDirectorInverseOneToOne;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelation\HasLargeRelationInverseOneToOne;
use My\Test\Project\Entity\Relations\Order\Traits\HasOrders\HasOrdersOneToMany;
use My\Test\Project\Entity\Repositories\PersonRepository;

// phpcs:enable
class Person implements 
    PersonInterface
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
	use HasAttributesAddressUnidirectionalManyToOne;
	use HasAttributesEmailsOneToMany;
	use HasCompanyDirectorInverseOneToOne;
	use HasOrdersOneToMany;
	use HasLargeRelationInverseOneToOne;

	/**
	 * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @param ClassMetadataBuilder $builder
	 */
	private static function setCustomRepositoryClass(ClassMetadataBuilder $builder) {
		$builder->setCustomRepositoryClass(PersonRepository::class);
	}

	public function __construct() {
		$this->runInitMethods();
	}
}
