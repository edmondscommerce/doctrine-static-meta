<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData013FieldInterface;

// phpcs:enable
trait LargeData013FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData013;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData013(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData013FieldInterface::PROP_LARGE_DATA013],
		            $builder,
		            LargeData013FieldInterface::DEFAULT_LARGE_DATA013
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
	protected static function validatorMetaForLargeData013(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData013FieldInterface::PROP_LARGE_DATA013,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData013(): ?bool {
		if (null === $this->largeData013) {
		    return LargeData013FieldInterface::DEFAULT_LARGE_DATA013;
		}
		return $this->largeData013;
	}

	/**
	 * @param bool|null $largeData013
	 * @return self
	 */
	public function setLargeData013(?bool $largeData013): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData013FieldInterface::PROP_LARGE_DATA013,
             $largeData013
        );
		return $this;
	}

	private function initLargeData013() {
		$this->largeData013 = LargeData013FieldInterface::DEFAULT_LARGE_DATA013;
	}
}
