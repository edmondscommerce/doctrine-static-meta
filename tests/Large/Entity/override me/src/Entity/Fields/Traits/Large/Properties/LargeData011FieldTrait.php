<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData011FieldInterface;

// phpcs:enable
trait LargeData011FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData011;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData011(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData011FieldInterface::PROP_LARGE_DATA011],
		            $builder,
		            LargeData011FieldInterface::DEFAULT_LARGE_DATA011
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
	protected static function validatorMetaForLargeData011(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData011FieldInterface::PROP_LARGE_DATA011,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData011(): ?bool {
		if (null === $this->largeData011) {
		    return LargeData011FieldInterface::DEFAULT_LARGE_DATA011;
		}
		return $this->largeData011;
	}

	/**
	 * @param bool|null $largeData011
	 * @return self
	 */
	public function setLargeData011(?bool $largeData011): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData011FieldInterface::PROP_LARGE_DATA011,
             $largeData011
        );
		return $this;
	}

	private function initLargeData011() {
		$this->largeData011 = LargeData011FieldInterface::DEFAULT_LARGE_DATA011;
	}
}
