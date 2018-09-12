<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData026FieldInterface;

// phpcs:enable
trait LargeData026FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData026;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData026(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData026FieldInterface::PROP_LARGE_DATA026],
		            $builder,
		            LargeData026FieldInterface::DEFAULT_LARGE_DATA026
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
	protected static function validatorMetaForLargeData026(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData026FieldInterface::PROP_LARGE_DATA026,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData026(): ?bool {
		if (null === $this->largeData026) {
		    return LargeData026FieldInterface::DEFAULT_LARGE_DATA026;
		}
		return $this->largeData026;
	}

	/**
	 * @param bool|null $largeData026
	 * @return self
	 */
	public function setLargeData026(?bool $largeData026): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData026FieldInterface::PROP_LARGE_DATA026,
             $largeData026
        );
		return $this;
	}

	private function initLargeData026() {
		$this->largeData026 = LargeData026FieldInterface::DEFAULT_LARGE_DATA026;
	}
}
