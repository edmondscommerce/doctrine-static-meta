<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData031FieldInterface;

// phpcs:enable
trait LargeData031FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData031;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData031(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData031FieldInterface::PROP_LARGE_DATA031],
		            $builder,
		            LargeData031FieldInterface::DEFAULT_LARGE_DATA031
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
	protected static function validatorMetaForLargeData031(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData031FieldInterface::PROP_LARGE_DATA031,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData031(): ?bool {
		if (null === $this->largeData031) {
		    return LargeData031FieldInterface::DEFAULT_LARGE_DATA031;
		}
		return $this->largeData031;
	}

	/**
	 * @param bool|null $largeData031
	 * @return self
	 */
	public function setLargeData031(?bool $largeData031): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData031FieldInterface::PROP_LARGE_DATA031,
             $largeData031
        );
		return $this;
	}

	private function initLargeData031() {
		$this->largeData031 = LargeData031FieldInterface::DEFAULT_LARGE_DATA031;
	}
}
