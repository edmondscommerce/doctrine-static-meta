<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData017FieldInterface;

// phpcs:enable
trait LargeData017FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData017;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData017(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData017FieldInterface::PROP_LARGE_DATA017],
		            $builder,
		            LargeData017FieldInterface::DEFAULT_LARGE_DATA017
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
	protected static function validatorMetaForLargeData017(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData017FieldInterface::PROP_LARGE_DATA017,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData017(): ?bool {
		if (null === $this->largeData017) {
		    return LargeData017FieldInterface::DEFAULT_LARGE_DATA017;
		}
		return $this->largeData017;
	}

	/**
	 * @param bool|null $largeData017
	 * @return self
	 */
	public function setLargeData017(?bool $largeData017): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData017FieldInterface::PROP_LARGE_DATA017,
             $largeData017
        );
		return $this;
	}

	private function initLargeData017() {
		$this->largeData017 = LargeData017FieldInterface::DEFAULT_LARGE_DATA017;
	}
}
