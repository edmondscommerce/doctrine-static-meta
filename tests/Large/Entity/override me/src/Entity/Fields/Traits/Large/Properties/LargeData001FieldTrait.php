<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData001FieldInterface;

// phpcs:enable
trait LargeData001FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData001;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData001(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData001FieldInterface::PROP_LARGE_DATA001],
		            $builder,
		            LargeData001FieldInterface::DEFAULT_LARGE_DATA001
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
	protected static function validatorMetaForLargeData001(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData001FieldInterface::PROP_LARGE_DATA001,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData001(): ?bool {
		if (null === $this->largeData001) {
		    return LargeData001FieldInterface::DEFAULT_LARGE_DATA001;
		}
		return $this->largeData001;
	}

	/**
	 * @param bool|null $largeData001
	 * @return self
	 */
	public function setLargeData001(?bool $largeData001): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData001FieldInterface::PROP_LARGE_DATA001,
             $largeData001
        );
		return $this;
	}

	private function initLargeData001() {
		$this->largeData001 = LargeData001FieldInterface::DEFAULT_LARGE_DATA001;
	}
}
