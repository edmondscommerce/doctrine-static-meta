<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits;
// phpcs:disable Generic.Files.LineLength.TooLong

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Fields\Interfaces\BooleanFieldInterface;

// phpcs:enable
trait BooleanFieldTrait {

	/**
	 * @var bool|null
	 */
	private $boolean;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForBoolean(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [BooleanFieldInterface::PROP_BOOLEAN],
		            $builder,
		            BooleanFieldInterface::DEFAULT_BOOLEAN
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
	protected static function validatorMetaForBoolean(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            BooleanFieldInterface::PROP_BOOLEAN,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isBoolean(): ?bool {
		if (null === $this->boolean) {
		    return BooleanFieldInterface::DEFAULT_BOOLEAN;
		}
		return $this->boolean;
	}

	/**
	 * @param bool|null $boolean
	 * @return self
	 */
	public function setBoolean(?bool $boolean): self {
		$this->updatePropertyValueThenValidateAndNotify(
            BooleanFieldInterface::PROP_BOOLEAN,
             $boolean
        );
		return $this;
	}

	private function initBoolean() {
		$this->boolean = BooleanFieldInterface::DEFAULT_BOOLEAN;
	}
}
