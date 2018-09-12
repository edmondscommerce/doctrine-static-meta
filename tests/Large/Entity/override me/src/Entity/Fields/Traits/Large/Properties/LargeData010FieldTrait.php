<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData010FieldInterface;

// phpcs:enable
trait LargeData010FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData010;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData010(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData010FieldInterface::PROP_LARGE_DATA010],
		            $builder,
		            LargeData010FieldInterface::DEFAULT_LARGE_DATA010
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
	protected static function validatorMetaForLargeData010(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData010FieldInterface::PROP_LARGE_DATA010,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData010(): ?bool {
		if (null === $this->largeData010) {
		    return LargeData010FieldInterface::DEFAULT_LARGE_DATA010;
		}
		return $this->largeData010;
	}

	/**
	 * @param bool|null $largeData010
	 * @return self
	 */
	public function setLargeData010(?bool $largeData010): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData010FieldInterface::PROP_LARGE_DATA010,
             $largeData010
        );
		return $this;
	}

	private function initLargeData010() {
		$this->largeData010 = LargeData010FieldInterface::DEFAULT_LARGE_DATA010;
	}
}
