<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData002FieldInterface;

// phpcs:enable
trait LargeData002FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData002;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData002(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData002FieldInterface::PROP_LARGE_DATA002],
		            $builder,
		            LargeData002FieldInterface::DEFAULT_LARGE_DATA002
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
	protected static function validatorMetaForLargeData002(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData002FieldInterface::PROP_LARGE_DATA002,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData002(): ?bool {
		if (null === $this->largeData002) {
		    return LargeData002FieldInterface::DEFAULT_LARGE_DATA002;
		}
		return $this->largeData002;
	}

	/**
	 * @param bool|null $largeData002
	 * @return self
	 */
	public function setLargeData002(?bool $largeData002): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData002FieldInterface::PROP_LARGE_DATA002,
             $largeData002
        );
		return $this;
	}

	private function initLargeData002() {
		$this->largeData002 = LargeData002FieldInterface::DEFAULT_LARGE_DATA002;
	}
}
