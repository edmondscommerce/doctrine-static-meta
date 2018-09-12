<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData019FieldInterface;

// phpcs:enable
trait LargeData019FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData019;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData019(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData019FieldInterface::PROP_LARGE_DATA019],
		            $builder,
		            LargeData019FieldInterface::DEFAULT_LARGE_DATA019
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
	protected static function validatorMetaForLargeData019(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData019FieldInterface::PROP_LARGE_DATA019,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData019(): ?bool {
		if (null === $this->largeData019) {
		    return LargeData019FieldInterface::DEFAULT_LARGE_DATA019;
		}
		return $this->largeData019;
	}

	/**
	 * @param bool|null $largeData019
	 * @return self
	 */
	public function setLargeData019(?bool $largeData019): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData019FieldInterface::PROP_LARGE_DATA019,
             $largeData019
        );
		return $this;
	}

	private function initLargeData019() {
		$this->largeData019 = LargeData019FieldInterface::DEFAULT_LARGE_DATA019;
	}
}
