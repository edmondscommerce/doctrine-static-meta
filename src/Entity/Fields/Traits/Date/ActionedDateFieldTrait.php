<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\ActionedDateFieldInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait ActionedDateFieldTrait {

	/**
	 * @var \DateTime|null
	 */
	private $actionedDate;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function getPropertyDoctrineMetaForActionedDate(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleDatetimeFields(
		            [ActionedDateFieldInterface::PROP_ACTIONED_DATE],
		            $builder,
		            true
		        );
	}

	/**
	 * @return \DateTime|null
	 */
	public function getActionedDate(): ?\DateTime {
		return $this->actionedDate;
	}

	/**
	 * @param \DateTime|null $actionedDate
	 * @return $this|ActionedDateFieldInterface
	 */
	public function setActionedDate(?\DateTime $actionedDate) {
		$this->actionedDate = $actionedDate;
		return $this;
	}
}
