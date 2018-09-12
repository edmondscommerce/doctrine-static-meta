<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData032FieldInterface;

// phpcs:enable
trait LargeData032FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData032;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData032(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData032FieldInterface::PROP_LARGE_DATA032],
		            $builder,
		            LargeData032FieldInterface::DEFAULT_LARGE_DATA032
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
	protected static function validatorMetaForLargeData032(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData032FieldInterface::PROP_LARGE_DATA032,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData032(): ?bool {
		if (null === $this->largeData032) {
		    return LargeData032FieldInterface::DEFAULT_LARGE_DATA032;
		}
		return $this->largeData032;
	}

	/**
	 * @param bool|null $largeData032
	 * @return self
	 */
	public function setLargeData032(?bool $largeData032): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData032FieldInterface::PROP_LARGE_DATA032,
             $largeData032
        );
		return $this;
	}

	private function initLargeData032() {
		$this->largeData032 = LargeData032FieldInterface::DEFAULT_LARGE_DATA032;
	}
}
