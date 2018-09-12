<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData037FieldInterface;

// phpcs:enable
trait LargeData037FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData037;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData037(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData037FieldInterface::PROP_LARGE_DATA037],
		            $builder,
		            LargeData037FieldInterface::DEFAULT_LARGE_DATA037
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
	protected static function validatorMetaForLargeData037(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData037FieldInterface::PROP_LARGE_DATA037,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData037(): ?bool {
		if (null === $this->largeData037) {
		    return LargeData037FieldInterface::DEFAULT_LARGE_DATA037;
		}
		return $this->largeData037;
	}

	/**
	 * @param bool|null $largeData037
	 * @return self
	 */
	public function setLargeData037(?bool $largeData037): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData037FieldInterface::PROP_LARGE_DATA037,
             $largeData037
        );
		return $this;
	}

	private function initLargeData037() {
		$this->largeData037 = LargeData037FieldInterface::DEFAULT_LARGE_DATA037;
	}
}
