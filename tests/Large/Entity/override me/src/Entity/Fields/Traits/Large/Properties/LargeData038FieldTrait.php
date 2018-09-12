<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData038FieldInterface;

// phpcs:enable
trait LargeData038FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData038;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData038(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData038FieldInterface::PROP_LARGE_DATA038],
		            $builder,
		            LargeData038FieldInterface::DEFAULT_LARGE_DATA038
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
	protected static function validatorMetaForLargeData038(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData038FieldInterface::PROP_LARGE_DATA038,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData038(): ?bool {
		if (null === $this->largeData038) {
		    return LargeData038FieldInterface::DEFAULT_LARGE_DATA038;
		}
		return $this->largeData038;
	}

	/**
	 * @param bool|null $largeData038
	 * @return self
	 */
	public function setLargeData038(?bool $largeData038): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData038FieldInterface::PROP_LARGE_DATA038,
             $largeData038
        );
		return $this;
	}

	private function initLargeData038() {
		$this->largeData038 = LargeData038FieldInterface::DEFAULT_LARGE_DATA038;
	}
}
