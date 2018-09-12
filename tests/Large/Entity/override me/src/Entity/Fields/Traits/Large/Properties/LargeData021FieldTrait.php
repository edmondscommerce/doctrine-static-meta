<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData021FieldInterface;

// phpcs:enable
trait LargeData021FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData021;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData021(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData021FieldInterface::PROP_LARGE_DATA021],
		            $builder,
		            LargeData021FieldInterface::DEFAULT_LARGE_DATA021
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
	protected static function validatorMetaForLargeData021(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData021FieldInterface::PROP_LARGE_DATA021,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData021(): ?bool {
		if (null === $this->largeData021) {
		    return LargeData021FieldInterface::DEFAULT_LARGE_DATA021;
		}
		return $this->largeData021;
	}

	/**
	 * @param bool|null $largeData021
	 * @return self
	 */
	public function setLargeData021(?bool $largeData021): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData021FieldInterface::PROP_LARGE_DATA021,
             $largeData021
        );
		return $this;
	}

	private function initLargeData021() {
		$this->largeData021 = LargeData021FieldInterface::DEFAULT_LARGE_DATA021;
	}
}
