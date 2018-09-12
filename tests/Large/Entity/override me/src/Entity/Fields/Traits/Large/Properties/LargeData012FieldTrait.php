<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData012FieldInterface;

// phpcs:enable
trait LargeData012FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData012;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData012(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData012FieldInterface::PROP_LARGE_DATA012],
		            $builder,
		            LargeData012FieldInterface::DEFAULT_LARGE_DATA012
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
	protected static function validatorMetaForLargeData012(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData012FieldInterface::PROP_LARGE_DATA012,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData012(): ?bool {
		if (null === $this->largeData012) {
		    return LargeData012FieldInterface::DEFAULT_LARGE_DATA012;
		}
		return $this->largeData012;
	}

	/**
	 * @param bool|null $largeData012
	 * @return self
	 */
	public function setLargeData012(?bool $largeData012): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData012FieldInterface::PROP_LARGE_DATA012,
             $largeData012
        );
		return $this;
	}

	private function initLargeData012() {
		$this->largeData012 = LargeData012FieldInterface::DEFAULT_LARGE_DATA012;
	}
}
