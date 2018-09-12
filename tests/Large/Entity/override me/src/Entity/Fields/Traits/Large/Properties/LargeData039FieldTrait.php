<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData039FieldInterface;

// phpcs:enable
trait LargeData039FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData039;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData039(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData039FieldInterface::PROP_LARGE_DATA039],
		            $builder,
		            LargeData039FieldInterface::DEFAULT_LARGE_DATA039
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
	protected static function validatorMetaForLargeData039(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData039FieldInterface::PROP_LARGE_DATA039,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData039(): ?bool {
		if (null === $this->largeData039) {
		    return LargeData039FieldInterface::DEFAULT_LARGE_DATA039;
		}
		return $this->largeData039;
	}

	/**
	 * @param bool|null $largeData039
	 * @return self
	 */
	public function setLargeData039(?bool $largeData039): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData039FieldInterface::PROP_LARGE_DATA039,
             $largeData039
        );
		return $this;
	}

	private function initLargeData039() {
		$this->largeData039 = LargeData039FieldInterface::DEFAULT_LARGE_DATA039;
	}
}
