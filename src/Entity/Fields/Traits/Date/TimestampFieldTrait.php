<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Date;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Date\TimestampFieldInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait TimestampFieldTrait {

	/**
	 * @var \DateTime|null
	 */
	private $timestamp;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function getPropertyDoctrineMetaForTimestamp(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleDatetimeFields(
		            [TimestampFieldInterface::PROP_TIMESTAMP],
		            $builder,
		            true
		        );
	}

	/**
	 * @return \DateTime|null
	 */
	public function getTimestamp(): ?\DateTime {
		return $this->timestamp;
	}

	/**
	 * @param \DateTime|null $timestamp
	 * @return $this|TimestampFieldInterface
	 */
	public function setTimestamp(?\DateTime $timestamp) {
		$this->timestamp = $timestamp;
		return $this;
	}
}
