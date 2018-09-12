<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData023FieldInterface;

// phpcs:enable
trait LargeData023FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData023;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData023(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData023FieldInterface::PROP_LARGE_DATA023],
		            $builder,
		            LargeData023FieldInterface::DEFAULT_LARGE_DATA023
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
	protected static function validatorMetaForLargeData023(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData023FieldInterface::PROP_LARGE_DATA023,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData023(): ?bool {
		if (null === $this->largeData023) {
		    return LargeData023FieldInterface::DEFAULT_LARGE_DATA023;
		}
		return $this->largeData023;
	}

	/**
	 * @param bool|null $largeData023
	 * @return self
	 */
	public function setLargeData023(?bool $largeData023): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData023FieldInterface::PROP_LARGE_DATA023,
             $largeData023
        );
		return $this;
	}

	private function initLargeData023() {
		$this->largeData023 = LargeData023FieldInterface::DEFAULT_LARGE_DATA023;
	}
}
