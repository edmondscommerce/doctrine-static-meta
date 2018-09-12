<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Data;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Data\LargeDataThreeFieldInterface;

// phpcs:enable
trait LargeDataThreeFieldTrait {

	/**
	 * @var string|null
	 */
	private $largeDataThree;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeDataThree(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleTextFields(
		            [LargeDataThreeFieldInterface::PROP_LARGE_DATA_THREE],
		            $builder,
		            LargeDataThreeFieldInterface::DEFAULT_LARGE_DATA_THREE
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
	protected static function validatorMetaForLargeDataThree(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeDataThreeFieldInterface::PROP_LARGE_DATA_THREE,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return string|null
	 */
	public function getLargeDataThree(): ?string {
		if (null === $this->largeDataThree) {
		    return LargeDataThreeFieldInterface::DEFAULT_LARGE_DATA_THREE;
		}
		return $this->largeDataThree;
	}

	/**
	 * @param string|null $largeDataThree
	 * @return self
	 */
	public function setLargeDataThree(?string $largeDataThree): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeDataThreeFieldInterface::PROP_LARGE_DATA_THREE,
             $largeDataThree
        );
		return $this;
	}

	private function initLargeDataThree() {
		$this->largeDataThree = LargeDataThreeFieldInterface::DEFAULT_LARGE_DATA_THREE;
	}
}
