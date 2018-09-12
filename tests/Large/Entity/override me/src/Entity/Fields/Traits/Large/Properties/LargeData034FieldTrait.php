<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData034FieldInterface;

// phpcs:enable
trait LargeData034FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData034;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData034(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData034FieldInterface::PROP_LARGE_DATA034],
		            $builder,
		            LargeData034FieldInterface::DEFAULT_LARGE_DATA034
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
	protected static function validatorMetaForLargeData034(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData034FieldInterface::PROP_LARGE_DATA034,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData034(): ?bool {
		if (null === $this->largeData034) {
		    return LargeData034FieldInterface::DEFAULT_LARGE_DATA034;
		}
		return $this->largeData034;
	}

	/**
	 * @param bool|null $largeData034
	 * @return self
	 */
	public function setLargeData034(?bool $largeData034): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData034FieldInterface::PROP_LARGE_DATA034,
             $largeData034
        );
		return $this;
	}

	private function initLargeData034() {
		$this->largeData034 = LargeData034FieldInterface::DEFAULT_LARGE_DATA034;
	}
}
