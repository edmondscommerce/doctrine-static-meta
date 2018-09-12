<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData033FieldInterface;

// phpcs:enable
trait LargeData033FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData033;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData033(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData033FieldInterface::PROP_LARGE_DATA033],
		            $builder,
		            LargeData033FieldInterface::DEFAULT_LARGE_DATA033
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
	protected static function validatorMetaForLargeData033(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData033FieldInterface::PROP_LARGE_DATA033,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData033(): ?bool {
		if (null === $this->largeData033) {
		    return LargeData033FieldInterface::DEFAULT_LARGE_DATA033;
		}
		return $this->largeData033;
	}

	/**
	 * @param bool|null $largeData033
	 * @return self
	 */
	public function setLargeData033(?bool $largeData033): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData033FieldInterface::PROP_LARGE_DATA033,
             $largeData033
        );
		return $this;
	}

	private function initLargeData033() {
		$this->largeData033 = LargeData033FieldInterface::DEFAULT_LARGE_DATA033;
	}
}
