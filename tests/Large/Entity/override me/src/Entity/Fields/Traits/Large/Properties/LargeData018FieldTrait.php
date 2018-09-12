<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData018FieldInterface;

// phpcs:enable
trait LargeData018FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData018;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData018(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData018FieldInterface::PROP_LARGE_DATA018],
		            $builder,
		            LargeData018FieldInterface::DEFAULT_LARGE_DATA018
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
	protected static function validatorMetaForLargeData018(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData018FieldInterface::PROP_LARGE_DATA018,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData018(): ?bool {
		if (null === $this->largeData018) {
		    return LargeData018FieldInterface::DEFAULT_LARGE_DATA018;
		}
		return $this->largeData018;
	}

	/**
	 * @param bool|null $largeData018
	 * @return self
	 */
	public function setLargeData018(?bool $largeData018): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData018FieldInterface::PROP_LARGE_DATA018,
             $largeData018
        );
		return $this;
	}

	private function initLargeData018() {
		$this->largeData018 = LargeData018FieldInterface::DEFAULT_LARGE_DATA018;
	}
}
