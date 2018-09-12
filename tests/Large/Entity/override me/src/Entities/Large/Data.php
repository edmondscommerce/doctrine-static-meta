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
use My\Test\Project\Entity\Fields\Traits\Large\Data\LargeDataFourFieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Data\LargeDataOneFieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Data\LargeDataThreeFieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Data\LargeDataTwoFieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData001FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData002FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData003FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData004FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData005FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData006FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData007FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData008FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData009FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData010FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData011FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData012FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData013FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData014FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData015FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData016FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData017FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData018FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData019FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData020FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData021FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData022FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData023FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData024FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData025FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData026FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData027FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData028FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData029FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData030FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData031FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData032FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData033FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData034FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData035FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData036FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData037FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData038FieldTrait;
use My\Test\Project\Entity\Fields\Traits\Large\Properties\LargeData039FieldTrait;
use My\Test\Project\Entity\Fields\Traits\StringFieldTrait;
use My\Test\Project\Entity\Fields\Traits\TextFieldTrait;
use My\Test\Project\Entity\Interfaces\Large\DataInterface;
use My\Test\Project\Entity\Repositories\Large\DataRepository;

// phpcs:enable
class Data implements 
    DataInterface
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
	use LargeDataOneFieldTrait;
	use LargeDataTwoFieldTrait;
	use LargeDataThreeFieldTrait;
	use LargeDataFourFieldTrait;
	use LargeData001FieldTrait;
	use LargeData002FieldTrait;
	use LargeData003FieldTrait;
	use LargeData004FieldTrait;
	use LargeData005FieldTrait;
	use LargeData006FieldTrait;
	use LargeData007FieldTrait;
	use LargeData008FieldTrait;
	use LargeData009FieldTrait;
	use LargeData010FieldTrait;
	use LargeData011FieldTrait;
	use LargeData012FieldTrait;
	use LargeData013FieldTrait;
	use LargeData014FieldTrait;
	use LargeData015FieldTrait;
	use LargeData016FieldTrait;
	use LargeData017FieldTrait;
	use LargeData018FieldTrait;
	use LargeData019FieldTrait;
	use LargeData020FieldTrait;
	use LargeData021FieldTrait;
	use LargeData022FieldTrait;
	use LargeData023FieldTrait;
	use LargeData024FieldTrait;
	use LargeData025FieldTrait;
	use LargeData026FieldTrait;
	use LargeData027FieldTrait;
	use LargeData028FieldTrait;
	use LargeData029FieldTrait;
	use LargeData030FieldTrait;
	use LargeData031FieldTrait;
	use LargeData032FieldTrait;
	use LargeData033FieldTrait;
	use LargeData034FieldTrait;
	use LargeData035FieldTrait;
	use LargeData036FieldTrait;
	use LargeData037FieldTrait;
	use LargeData038FieldTrait;
	use LargeData039FieldTrait;

	/**
	 * This is called in UsesPHPMetaDataTrait::loadClassDoctrineMetaData
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 * @param ClassMetadataBuilder $builder
	 */
	private static function setCustomRepositoryClass(ClassMetadataBuilder $builder) {
		$builder->setCustomRepositoryClass(DataRepository::class);
	}

	public function __construct() {
		$this->runInitMethods();
	}
}
