<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\DatetimeFieldInterface;

// phpcs:enable
trait DatetimeFieldTrait {

	/**
	 * @var \DateTime|null
	 */
	private $datetime;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForDatetime(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleDatetimeFields(
		            [DatetimeFieldInterface::PROP_DATETIME],
		            $builder,
		            DatetimeFieldInterface::DEFAULT_DATETIME
		        );
	}

	/**
	 * This method sets the validation for this field.
	 *
	 * You should add in as many relevant property constraints as you see fit.
	 * 
	 * Remove the PHPMD suppressed warning once you start setting constraints
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 * @see https://symfony.com/doc/current/validation.html#supported-constraints
	 * @param ValidatorClassMetaData $metadata
	 * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
	 * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
	 * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
	 */
	protected static function validatorMetaForDatetime(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            DatetimeFieldInterface::PROP_DATETIME,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return \DateTime|null
	 */
	public function getDatetime(): ?\DateTime {
		if (null === $this->datetime) {
		    return DatetimeFieldInterface::DEFAULT_DATETIME;
		}
		return $this->datetime;
	}

	/**
	 * @param \DateTime|null $datetime
	 * @return self
	 */
	public function setDatetime(?\DateTime $datetime): self {
		$this->updatePropertyValueThenValidateAndNotify(
            DatetimeFieldInterface::PROP_DATETIME,
             $datetime
        );
		return $this;
	}

	private function initDatetime() {
		$this->datetime = DatetimeFieldInterface::DEFAULT_DATETIME;
	}
}
