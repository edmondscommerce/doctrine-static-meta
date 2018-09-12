<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData014FieldInterface;

// phpcs:enable
trait LargeData014FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData014;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData014(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData014FieldInterface::PROP_LARGE_DATA014],
		            $builder,
		            LargeData014FieldInterface::DEFAULT_LARGE_DATA014
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
	protected static function validatorMetaForLargeData014(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData014FieldInterface::PROP_LARGE_DATA014,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData014(): ?bool {
		if (null === $this->largeData014) {
		    return LargeData014FieldInterface::DEFAULT_LARGE_DATA014;
		}
		return $this->largeData014;
	}

	/**
	 * @param bool|null $largeData014
	 * @return self
	 */
	public function setLargeData014(?bool $largeData014): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData014FieldInterface::PROP_LARGE_DATA014,
             $largeData014
        );
		return $this;
	}

	private function initLargeData014() {
		$this->largeData014 = LargeData014FieldInterface::DEFAULT_LARGE_DATA014;
	}
}
