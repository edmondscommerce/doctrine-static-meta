<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData028FieldInterface;

// phpcs:enable
trait LargeData028FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData028;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData028(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData028FieldInterface::PROP_LARGE_DATA028],
		            $builder,
		            LargeData028FieldInterface::DEFAULT_LARGE_DATA028
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
	protected static function validatorMetaForLargeData028(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData028FieldInterface::PROP_LARGE_DATA028,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData028(): ?bool {
		if (null === $this->largeData028) {
		    return LargeData028FieldInterface::DEFAULT_LARGE_DATA028;
		}
		return $this->largeData028;
	}

	/**
	 * @param bool|null $largeData028
	 * @return self
	 */
	public function setLargeData028(?bool $largeData028): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData028FieldInterface::PROP_LARGE_DATA028,
             $largeData028
        );
		return $this;
	}

	private function initLargeData028() {
		$this->largeData028 = LargeData028FieldInterface::DEFAULT_LARGE_DATA028;
	}
}
