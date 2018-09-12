<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Data;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Data\LargeDataTwoFieldInterface;

// phpcs:enable
trait LargeDataTwoFieldTrait {

	/**
	 * @var string|null
	 */
	private $largeDataTwo;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeDataTwo(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleTextFields(
		            [LargeDataTwoFieldInterface::PROP_LARGE_DATA_TWO],
		            $builder,
		            LargeDataTwoFieldInterface::DEFAULT_LARGE_DATA_TWO
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
	protected static function validatorMetaForLargeDataTwo(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeDataTwoFieldInterface::PROP_LARGE_DATA_TWO,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return string|null
	 */
	public function getLargeDataTwo(): ?string {
		if (null === $this->largeDataTwo) {
		    return LargeDataTwoFieldInterface::DEFAULT_LARGE_DATA_TWO;
		}
		return $this->largeDataTwo;
	}

	/**
	 * @param string|null $largeDataTwo
	 * @return self
	 */
	public function setLargeDataTwo(?string $largeDataTwo): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeDataTwoFieldInterface::PROP_LARGE_DATA_TWO,
             $largeDataTwo
        );
		return $this;
	}

	private function initLargeDataTwo() {
		$this->largeDataTwo = LargeDataTwoFieldInterface::DEFAULT_LARGE_DATA_TWO;
	}
}
