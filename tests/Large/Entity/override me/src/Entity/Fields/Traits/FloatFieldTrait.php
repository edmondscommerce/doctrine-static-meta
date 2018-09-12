<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\FloatFieldInterface;

// phpcs:enable
trait FloatFieldTrait {

	/**
	 * @var float|null
	 */
	private $float;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForFloat(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleFloatFields(
		            [FloatFieldInterface::PROP_FLOAT],
		            $builder,
		            FloatFieldInterface::DEFAULT_FLOAT
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
	protected static function validatorMetaForFloat(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            FloatFieldInterface::PROP_FLOAT,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return float|null
	 */
	public function getFloat(): ?float {
		if (null === $this->float) {
		    return FloatFieldInterface::DEFAULT_FLOAT;
		}
		return $this->float;
	}

	/**
	 * @param float|null $float
	 * @return self
	 */
	public function setFloat(?float $float): self {
		$this->updatePropertyValueThenValidateAndNotify(
            FloatFieldInterface::PROP_FLOAT,
             $float
        );
		return $this;
	}

	private function initFloat() {
		$this->float = FloatFieldInterface::DEFAULT_FLOAT;
	}
}
