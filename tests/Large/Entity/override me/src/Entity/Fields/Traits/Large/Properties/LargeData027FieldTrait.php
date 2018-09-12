<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData027FieldInterface;

// phpcs:enable
trait LargeData027FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData027;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData027(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData027FieldInterface::PROP_LARGE_DATA027],
		            $builder,
		            LargeData027FieldInterface::DEFAULT_LARGE_DATA027
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
	protected static function validatorMetaForLargeData027(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData027FieldInterface::PROP_LARGE_DATA027,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData027(): ?bool {
		if (null === $this->largeData027) {
		    return LargeData027FieldInterface::DEFAULT_LARGE_DATA027;
		}
		return $this->largeData027;
	}

	/**
	 * @param bool|null $largeData027
	 * @return self
	 */
	public function setLargeData027(?bool $largeData027): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData027FieldInterface::PROP_LARGE_DATA027,
             $largeData027
        );
		return $this;
	}

	private function initLargeData027() {
		$this->largeData027 = LargeData027FieldInterface::DEFAULT_LARGE_DATA027;
	}
}
