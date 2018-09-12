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
use My\Test\Project\Entity\Interfaces\CompanyInterface;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits\HasAnotherDeeplyNestedClient\HasAnotherDeeplyNestedClientOwningOneToOne;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddresses\HasAttributesAddressesOneToMany;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmails\HasAttributesEmailsUnidirectionalOneToMany;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectors\HasCompanyDirectorsOwningManyToMany;
use My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClient\HasSomeClientOwningOneToOne;
use My\Test\Project\Entity\Repositories\CompanyRepository;

// phpcs:enable
class Company implements 
    CompanyInterface
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
	use HasCompanyDirectorsOwningManyToMany;
	use HasAttributesAddressesOneToMany;
	use HasAttributesEmailsUnidirectionalOneToMany;
	use HasSomeClientOwningOneToOne;
	use HasAnotherDeeplyNestedClientOwningOneToOne;

	/**
	 * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @param ClassMetadataBuilder $builder
	 */
	private static function setCustomRepositoryClass(ClassMetadataBuilder $builder) {
		$builder->setCustomRepositoryClass(CompanyRepository::class);
	}

	public function __construct() {
		$this->runInitMethods();
	}
}
