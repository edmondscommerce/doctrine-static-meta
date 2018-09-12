<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData007FieldInterface;

// phpcs:enable
trait LargeData007FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData007;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData007(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData007FieldInterface::PROP_LARGE_DATA007],
		            $builder,
		            LargeData007FieldInterface::DEFAULT_LARGE_DATA007
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
	protected static function validatorMetaForLargeData007(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData007FieldInterface::PROP_LARGE_DATA007,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData007(): ?bool {
		if (null === $this->largeData007) {
		    return LargeData007FieldInterface::DEFAULT_LARGE_DATA007;
		}
		return $this->largeData007;
	}

	/**
	 * @param bool|null $largeData007
	 * @return self
	 */
	public function setLargeData007(?bool $largeData007): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData007FieldInterface::PROP_LARGE_DATA007,
             $largeData007
        );
		return $this;
	}

	private function initLargeData007() {
		$this->largeData007 = LargeData007FieldInterface::DEFAULT_LARGE_DATA007;
	}
}
