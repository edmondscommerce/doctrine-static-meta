<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Large;
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
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddress\HasAttributesAddressUnidirectionalManyToOne;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmails\HasAttributesEmailsOneToMany;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectors\HasCompanyDirectorsOwningManyToMany;
use My\Test\Project\Entity\Relations\Company\Traits\HasCompany\HasCompanyUnidirectionalOneToOne;
use My\Test\Project\Entity\Relations\Large\Data\Traits\HasLargeDatas\HasLargeDatasUnidirectionalOneToMany;
use My\Test\Project\Entity\Relations\Large\Property\Traits\HasLargeProperty\HasLargePropertyManyToOne;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddresses\HasOrderAddressesOneToMany;
use My\Test\Project\Entity\Relations\Person\Traits\HasPerson\HasPersonOwningOneToOne;
use My\Test\Project\Entity\Repositories\Large\RelationRepository;

// phpcs:enable
class Relation implements 
    RelationInterface
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
	use HasCompanyDirectorsOwningManyToMany;
	use HasLargeDatasUnidirectionalOneToMany;
	use HasPersonOwningOneToOne;
	use HasLargePropertyManyToOne;
	use HasOrderAddressesOneToMany;
	use HasCompanyUnidirectionalOneToOne;

	/**
	 * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @param ClassMetadataBuilder $builder
	 */
	private static function setCustomRepositoryClass(ClassMetadataBuilder $builder) {
		$builder->setCustomRepositoryClass(RelationRepository::class);
	}

	public function __construct() {
		$this->runInitMethods();
	}
}
