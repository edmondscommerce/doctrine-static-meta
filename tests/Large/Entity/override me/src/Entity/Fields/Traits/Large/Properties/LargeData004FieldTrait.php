<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData004FieldInterface;

// phpcs:enable
trait LargeData004FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData004;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData004(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData004FieldInterface::PROP_LARGE_DATA004],
		            $builder,
		            LargeData004FieldInterface::DEFAULT_LARGE_DATA004
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
	protected static function validatorMetaForLargeData004(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData004FieldInterface::PROP_LARGE_DATA004,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData004(): ?bool {
		if (null === $this->largeData004) {
		    return LargeData004FieldInterface::DEFAULT_LARGE_DATA004;
		}
		return $this->largeData004;
	}

	/**
	 * @param bool|null $largeData004
	 * @return self
	 */
	public function setLargeData004(?bool $largeData004): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData004FieldInterface::PROP_LARGE_DATA004,
             $largeData004
        );
		return $this;
	}

	private function initLargeData004() {
		$this->largeData004 = LargeData004FieldInterface::DEFAULT_LARGE_DATA004;
	}
}
