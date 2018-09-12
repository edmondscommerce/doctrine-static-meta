<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData029FieldInterface;

// phpcs:enable
trait LargeData029FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData029;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData029(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData029FieldInterface::PROP_LARGE_DATA029],
		            $builder,
		            LargeData029FieldInterface::DEFAULT_LARGE_DATA029
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
	protected static function validatorMetaForLargeData029(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData029FieldInterface::PROP_LARGE_DATA029,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData029(): ?bool {
		if (null === $this->largeData029) {
		    return LargeData029FieldInterface::DEFAULT_LARGE_DATA029;
		}
		return $this->largeData029;
	}

	/**
	 * @param bool|null $largeData029
	 * @return self
	 */
	public function setLargeData029(?bool $largeData029): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData029FieldInterface::PROP_LARGE_DATA029,
             $largeData029
        );
		return $this;
	}

	private function initLargeData029() {
		$this->largeData029 = LargeData029FieldInterface::DEFAULT_LARGE_DATA029;
	}
}
