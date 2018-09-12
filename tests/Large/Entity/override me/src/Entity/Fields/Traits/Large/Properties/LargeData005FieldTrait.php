<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData005FieldInterface;

// phpcs:enable
trait LargeData005FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData005;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData005(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData005FieldInterface::PROP_LARGE_DATA005],
		            $builder,
		            LargeData005FieldInterface::DEFAULT_LARGE_DATA005
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
	protected static function validatorMetaForLargeData005(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData005FieldInterface::PROP_LARGE_DATA005,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData005(): ?bool {
		if (null === $this->largeData005) {
		    return LargeData005FieldInterface::DEFAULT_LARGE_DATA005;
		}
		return $this->largeData005;
	}

	/**
	 * @param bool|null $largeData005
	 * @return self
	 */
	public function setLargeData005(?bool $largeData005): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData005FieldInterface::PROP_LARGE_DATA005,
             $largeData005
        );
		return $this;
	}

	private function initLargeData005() {
		$this->largeData005 = LargeData005FieldInterface::DEFAULT_LARGE_DATA005;
	}
}
