<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData008FieldInterface;

// phpcs:enable
trait LargeData008FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData008;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData008(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData008FieldInterface::PROP_LARGE_DATA008],
		            $builder,
		            LargeData008FieldInterface::DEFAULT_LARGE_DATA008
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
	protected static function validatorMetaForLargeData008(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData008FieldInterface::PROP_LARGE_DATA008,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData008(): ?bool {
		if (null === $this->largeData008) {
		    return LargeData008FieldInterface::DEFAULT_LARGE_DATA008;
		}
		return $this->largeData008;
	}

	/**
	 * @param bool|null $largeData008
	 * @return self
	 */
	public function setLargeData008(?bool $largeData008): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData008FieldInterface::PROP_LARGE_DATA008,
             $largeData008
        );
		return $this;
	}

	private function initLargeData008() {
		$this->largeData008 = LargeData008FieldInterface::DEFAULT_LARGE_DATA008;
	}
}
