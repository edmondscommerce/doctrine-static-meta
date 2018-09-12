<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Data;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Data\LargeDataFourFieldInterface;

// phpcs:enable
trait LargeDataFourFieldTrait {

	/**
	 * @var string|null
	 */
	private $largeDataFour;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeDataFour(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleTextFields(
		            [LargeDataFourFieldInterface::PROP_LARGE_DATA_FOUR],
		            $builder,
		            LargeDataFourFieldInterface::DEFAULT_LARGE_DATA_FOUR
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
	protected static function validatorMetaForLargeDataFour(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeDataFourFieldInterface::PROP_LARGE_DATA_FOUR,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return string|null
	 */
	public function getLargeDataFour(): ?string {
		if (null === $this->largeDataFour) {
		    return LargeDataFourFieldInterface::DEFAULT_LARGE_DATA_FOUR;
		}
		return $this->largeDataFour;
	}

	/**
	 * @param string|null $largeDataFour
	 * @return self
	 */
	public function setLargeDataFour(?string $largeDataFour): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeDataFourFieldInterface::PROP_LARGE_DATA_FOUR,
             $largeDataFour
        );
		return $this;
	}

	private function initLargeDataFour() {
		$this->largeDataFour = LargeDataFourFieldInterface::DEFAULT_LARGE_DATA_FOUR;
	}
}
