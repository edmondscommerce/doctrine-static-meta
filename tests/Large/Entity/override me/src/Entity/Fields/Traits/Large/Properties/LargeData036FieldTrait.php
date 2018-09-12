<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData036FieldInterface;

// phpcs:enable
trait LargeData036FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData036;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData036(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData036FieldInterface::PROP_LARGE_DATA036],
		            $builder,
		            LargeData036FieldInterface::DEFAULT_LARGE_DATA036
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
	protected static function validatorMetaForLargeData036(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData036FieldInterface::PROP_LARGE_DATA036,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData036(): ?bool {
		if (null === $this->largeData036) {
		    return LargeData036FieldInterface::DEFAULT_LARGE_DATA036;
		}
		return $this->largeData036;
	}

	/**
	 * @param bool|null $largeData036
	 * @return self
	 */
	public function setLargeData036(?bool $largeData036): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData036FieldInterface::PROP_LARGE_DATA036,
             $largeData036
        );
		return $this;
	}

	private function initLargeData036() {
		$this->largeData036 = LargeData036FieldInterface::DEFAULT_LARGE_DATA036;
	}
}
