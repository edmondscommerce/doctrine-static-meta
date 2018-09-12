<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\DecimalFieldInterface;

// phpcs:enable
trait DecimalFieldTrait {

	/**
	 * 
	 */
	private $decimal;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForDecimal(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleDecimalFields(
		            [DecimalFieldInterface::PROP_DECIMAL],
		            $builder,
		            DecimalFieldInterface::DEFAULT_DECIMAL
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
	protected static function validatorMetaForDecimal(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            DecimalFieldInterface::PROP_DECIMAL,
		//            new NotBlank()
		//        );
	}

	/**
	 * 
	 */
	public function getDecimal() {
		if (null === $this->decimal) {
		    return DecimalFieldInterface::DEFAULT_DECIMAL;
		}
		return $this->decimal;
	}

	/**
	 *  $decimal
	 * @return self
	 */
	public function setDecimal($decimal): self {
		$this->updatePropertyValueThenValidateAndNotify(
            DecimalFieldInterface::PROP_DECIMAL,
             $decimal
        );
		return $this;
	}

	private function initDecimal() {
		$this->decimal = DecimalFieldInterface::DEFAULT_DECIMAL;
	}
}
