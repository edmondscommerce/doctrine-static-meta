<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData024FieldInterface;

// phpcs:enable
trait LargeData024FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData024;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData024(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData024FieldInterface::PROP_LARGE_DATA024],
		            $builder,
		            LargeData024FieldInterface::DEFAULT_LARGE_DATA024
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
	protected static function validatorMetaForLargeData024(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData024FieldInterface::PROP_LARGE_DATA024,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData024(): ?bool {
		if (null === $this->largeData024) {
		    return LargeData024FieldInterface::DEFAULT_LARGE_DATA024;
		}
		return $this->largeData024;
	}

	/**
	 * @param bool|null $largeData024
	 * @return self
	 */
	public function setLargeData024(?bool $largeData024): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData024FieldInterface::PROP_LARGE_DATA024,
             $largeData024
        );
		return $this;
	}

	private function initLargeData024() {
		$this->largeData024 = LargeData024FieldInterface::DEFAULT_LARGE_DATA024;
	}
}
