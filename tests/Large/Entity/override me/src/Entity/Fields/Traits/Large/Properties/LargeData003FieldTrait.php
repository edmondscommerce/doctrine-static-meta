<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData003FieldInterface;

// phpcs:enable
trait LargeData003FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData003;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData003(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData003FieldInterface::PROP_LARGE_DATA003],
		            $builder,
		            LargeData003FieldInterface::DEFAULT_LARGE_DATA003
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
	protected static function validatorMetaForLargeData003(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData003FieldInterface::PROP_LARGE_DATA003,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData003(): ?bool {
		if (null === $this->largeData003) {
		    return LargeData003FieldInterface::DEFAULT_LARGE_DATA003;
		}
		return $this->largeData003;
	}

	/**
	 * @param bool|null $largeData003
	 * @return self
	 */
	public function setLargeData003(?bool $largeData003): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData003FieldInterface::PROP_LARGE_DATA003,
             $largeData003
        );
		return $this;
	}

	private function initLargeData003() {
		$this->largeData003 = LargeData003FieldInterface::DEFAULT_LARGE_DATA003;
	}
}
