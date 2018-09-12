<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Large\Properties;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\Large\Properties\LargeData015FieldInterface;

// phpcs:enable
trait LargeData015FieldTrait {

	/**
	 * @var bool|null
	 */
	private $largeData015;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForLargeData015(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [LargeData015FieldInterface::PROP_LARGE_DATA015],
		            $builder,
		            LargeData015FieldInterface::DEFAULT_LARGE_DATA015
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
	protected static function validatorMetaForLargeData015(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            LargeData015FieldInterface::PROP_LARGE_DATA015,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isLargeData015(): ?bool {
		if (null === $this->largeData015) {
		    return LargeData015FieldInterface::DEFAULT_LARGE_DATA015;
		}
		return $this->largeData015;
	}

	/**
	 * @param bool|null $largeData015
	 * @return self
	 */
	public function setLargeData015(?bool $largeData015): self {
		$this->updatePropertyValueThenValidateAndNotify(
            LargeData015FieldInterface::PROP_LARGE_DATA015,
             $largeData015
        );
		return $this;
	}

	private function initLargeData015() {
		$this->largeData015 = LargeData015FieldInterface::DEFAULT_LARGE_DATA015;
	}
}
