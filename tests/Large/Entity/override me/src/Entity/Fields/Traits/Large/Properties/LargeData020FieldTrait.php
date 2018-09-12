<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData020FieldInterface;

// phpcs:enable
trait LargeData020FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData020;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData020(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData020FieldInterface::PROP_LARGE_DATA020],
		            $builder,
		            LargeData020FieldInterface::DEFAULT_LARGE_DATA020
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
	protected static function validatorMetaForLargeData020(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData020FieldInterface::PROP_LARGE_DATA020,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData020(): ?bool {
		if (null === $this->largeData020) {
		    return LargeData020FieldInterface::DEFAULT_LARGE_DATA020;
		}
		return $this->largeData020;
	}

	/**
	 * @param bool|null $largeData020
	 * @return self
	 */
	public function setLargeData020(?bool $largeData020): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData020FieldInterface::PROP_LARGE_DATA020,
             $largeData020
        );
		return $this;
	}

	private function initLargeData020() {
		$this->largeData020 = LargeData020FieldInterface::DEFAULT_LARGE_DATA020;
	}
}
