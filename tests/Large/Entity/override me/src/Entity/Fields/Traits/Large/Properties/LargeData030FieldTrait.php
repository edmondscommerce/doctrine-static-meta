<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData030FieldInterface;

// phpcs:enable
trait LargeData030FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData030;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData030(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData030FieldInterface::PROP_LARGE_DATA030],
		            $builder,
		            LargeData030FieldInterface::DEFAULT_LARGE_DATA030
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
	protected static function validatorMetaForLargeData030(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData030FieldInterface::PROP_LARGE_DATA030,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData030(): ?bool {
		if (null === $this->largeData030) {
		    return LargeData030FieldInterface::DEFAULT_LARGE_DATA030;
		}
		return $this->largeData030;
	}

	/**
	 * @param bool|null $largeData030
	 * @return self
	 */
	public function setLargeData030(?bool $largeData030): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData030FieldInterface::PROP_LARGE_DATA030,
             $largeData030
        );
		return $this;
	}

	private function initLargeData030() {
		$this->largeData030 = LargeData030FieldInterface::DEFAULT_LARGE_DATA030;
	}
}
