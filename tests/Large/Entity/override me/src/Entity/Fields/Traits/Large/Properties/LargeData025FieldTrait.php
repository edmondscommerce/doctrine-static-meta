<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData025FieldInterface;

// phpcs:enable
trait LargeData025FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData025;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData025(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData025FieldInterface::PROP_LARGE_DATA025],
		            $builder,
		            LargeData025FieldInterface::DEFAULT_LARGE_DATA025
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
	protected static function validatorMetaForLargeData025(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData025FieldInterface::PROP_LARGE_DATA025,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData025(): ?bool {
		if (null === $this->largeData025) {
		    return LargeData025FieldInterface::DEFAULT_LARGE_DATA025;
		}
		return $this->largeData025;
	}

	/**
	 * @param bool|null $largeData025
	 * @return self
	 */
	public function setLargeData025(?bool $largeData025): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData025FieldInterface::PROP_LARGE_DATA025,
             $largeData025
        );
		return $this;
	}

	private function initLargeData025() {
		$this->largeData025 = LargeData025FieldInterface::DEFAULT_LARGE_DATA025;
	}
}
