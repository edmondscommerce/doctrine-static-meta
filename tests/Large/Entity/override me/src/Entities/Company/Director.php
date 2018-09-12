<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Company;
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
use My\Test\Project\Entity\Interfaces\Company\DirectorInterface;
use My\Test\Project\Entity\Relations\Company\Traits\HasCompanies\HasCompaniesInverseManyToMany;
use My\Test\Project\Entity\Relations\Large\Relation\Traits\HasLargeRelations\HasLargeRelationsInverseManyToMany;
use My\Test\Project\Entity\Relations\Person\Traits\HasPerson\HasPersonOwningOneToOne;
use My\Test\Project\Entity\Repositories\Company\DirectorRepository;

// phpcs:enable
class Director implements 
    DirectorInterface
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
	use HasCompaniesInverseManyToMany;
	use HasPersonOwningOneToOne;
	use HasLargeRelationsInverseManyToMany;

	/**
	 * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @param ClassMetadataBuilder $builder
	 */
	private static function setCustomRepositoryClass(ClassMetadataBuilder $builder) {
		$builder->setCustomRepositoryClass(DirectorRepository::class);
	}

	public function __construct() {
		$this->runInitMethods();
	}
}
