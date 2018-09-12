<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData035FieldInterface;

// phpcs:enable
trait LargeData035FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData035;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData035(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData035FieldInterface::PROP_LARGE_DATA035],
		            $builder,
		            LargeData035FieldInterface::DEFAULT_LARGE_DATA035
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
	protected static function validatorMetaForLargeData035(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData035FieldInterface::PROP_LARGE_DATA035,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData035(): ?bool {
		if (null === $this->largeData035) {
		    return LargeData035FieldInterface::DEFAULT_LARGE_DATA035;
		}
		return $this->largeData035;
	}

	/**
	 * @param bool|null $largeData035
	 * @return self
	 */
	public function setLargeData035(?bool $largeData035): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData035FieldInterface::PROP_LARGE_DATA035,
             $largeData035
        );
		return $this;
	}

	private function initLargeData035() {
		$this->largeData035 = LargeData035FieldInterface::DEFAULT_LARGE_DATA035;
	}
}
