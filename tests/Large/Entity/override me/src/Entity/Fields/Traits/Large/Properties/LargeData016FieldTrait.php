<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData016FieldInterface;

// phpcs:enable
trait LargeData016FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData016;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData016(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData016FieldInterface::PROP_LARGE_DATA016],
		            $builder,
		            LargeData016FieldInterface::DEFAULT_LARGE_DATA016
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
	protected static function validatorMetaForLargeData016(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData016FieldInterface::PROP_LARGE_DATA016,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData016(): ?bool {
		if (null === $this->largeData016) {
		    return LargeData016FieldInterface::DEFAULT_LARGE_DATA016;
		}
		return $this->largeData016;
	}

	/**
	 * @param bool|null $largeData016
	 * @return self
	 */
	public function setLargeData016(?bool $largeData016): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData016FieldInterface::PROP_LARGE_DATA016,
             $largeData016
        );
		return $this;
	}

	private function initLargeData016() {
		$this->largeData016 = LargeData016FieldInterface::DEFAULT_LARGE_DATA016;
	}
}
