<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData006FieldInterface;

// phpcs:enable
trait LargeData006FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData006;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData006(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData006FieldInterface::PROP_LARGE_DATA006],
		            $builder,
		            LargeData006FieldInterface::DEFAULT_LARGE_DATA006
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
	protected static function validatorMetaForLargeData006(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData006FieldInterface::PROP_LARGE_DATA006,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData006(): ?bool {
		if (null === $this->largeData006) {
		    return LargeData006FieldInterface::DEFAULT_LARGE_DATA006;
		}
		return $this->largeData006;
	}

	/**
	 * @param bool|null $largeData006
	 * @return self
	 */
	public function setLargeData006(?bool $largeData006): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData006FieldInterface::PROP_LARGE_DATA006,
             $largeData006
        );
		return $this;
	}

	private function initLargeData006() {
		$this->largeData006 = LargeData006FieldInterface::DEFAULT_LARGE_DATA006;
	}
}
