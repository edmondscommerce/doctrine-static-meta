<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Data;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Data\LargeDataOneFieldInterface;

// phpcs:enable
trait LargeDataOneFieldTrait {

	/**
	 * @var string|null
	 */
	private $largeDataOne;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeDataOne(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleTextFields(
		            [LargeDataOneFieldInterface::PROP_LARGE_DATA_ONE],
		            $builder,
		            LargeDataOneFieldInterface::DEFAULT_LARGE_DATA_ONE
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
	protected static function validatorMetaForLargeDataOne(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeDataOneFieldInterface::PROP_LARGE_DATA_ONE,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return string|null
	 */
	public function getLargeDataOne(): ?string {
		if (null === $this->largeDataOne) {
		    return LargeDataOneFieldInterface::DEFAULT_LARGE_DATA_ONE;
		}
		return $this->largeDataOne;
	}

	/**
	 * @param string|null $largeDataOne
	 * @return self
	 */
	public function setLargeDataOne(?string $largeDataOne): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeDataOneFieldInterface::PROP_LARGE_DATA_ONE,
             $largeDataOne
        );
		return $this;
	}

	private function initLargeDataOne() {
		$this->largeDataOne = LargeDataOneFieldInterface::DEFAULT_LARGE_DATA_ONE;
	}
}
