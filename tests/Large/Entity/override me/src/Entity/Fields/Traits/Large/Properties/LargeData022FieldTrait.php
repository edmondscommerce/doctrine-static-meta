<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData022FieldInterface;

// phpcs:enable
trait LargeData022FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData022;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData022(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData022FieldInterface::PROP_LARGE_DATA022],
		            $builder,
		            LargeData022FieldInterface::DEFAULT_LARGE_DATA022
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
	protected static function validatorMetaForLargeData022(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData022FieldInterface::PROP_LARGE_DATA022,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData022(): ?bool {
		if (null === $this->largeData022) {
		    return LargeData022FieldInterface::DEFAULT_LARGE_DATA022;
		}
		return $this->largeData022;
	}

	/**
	 * @param bool|null $largeData022
	 * @return self
	 */
	public function setLargeData022(?bool $largeData022): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData022FieldInterface::PROP_LARGE_DATA022,
             $largeData022
        );
		return $this;
	}

	private function initLargeData022() {
		$this->largeData022 = LargeData022FieldInterface::DEFAULT_LARGE_DATA022;
	}
}
