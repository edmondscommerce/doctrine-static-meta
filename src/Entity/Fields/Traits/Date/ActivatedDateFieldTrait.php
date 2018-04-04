<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\ActivatedDateFieldInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait ActivatedDateFieldTrait {

	/**
	 * @var \DateTime|null
	 */
	private $activatedDate;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function getPropertyDoctrineMetaForActivatedDate(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleDatetimeFields(
		            [ActivatedDateFieldInterface::PROP_ACTIVATED_DATE],
		            $builder,
		            true
		        );
	}

	/**
	 * @return \DateTime|null
	 */
	public function getActivatedDate(): ?\DateTime {
		return $this->activatedDate;
	}

	/**
	 * @param \DateTime|null $activatedDate
	 * @return $this|ActivatedDateFieldInterface
	 */
	public function setActivatedDate(?\DateTime $activatedDate) {
		$this->activatedDate = $activatedDate;
		return $this;
	}
}
