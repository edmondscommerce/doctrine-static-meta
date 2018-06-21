<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsDisabledFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait DefaultsDisabledFieldTrait {

	/**
	 * @var bool|null
	 */
	private $defaultsDisabled;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForDefaultsDisabled(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [DefaultsDisabledFieldInterface::PROP_DEFAULTS_DISABLED],
		            $builder,
		            DefaultsDisabledFieldInterface::DEFAULT_DEFAULTS_DISABLED
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
	protected static function validatorMetaForDefaultsDisabled(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            DefaultsDisabledFieldInterface::PROP_DEFAULTS_DISABLED,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isDefaultsDisabled(): ?bool {
		if (null === $this->defaultsDisabled) {
		    return DefaultsDisabledFieldInterface::DEFAULT_DEFAULTS_DISABLED;
		}
		return $this->defaultsDisabled;
	}

	/**
	 * @param bool|null $defaultsDisabled
	 * @return self
	 */
	public function setDefaultsDisabled(?bool $defaultsDisabled): self {
		$this->defaultsDisabled = $defaultsDisabled;
		if ($this instanceof ValidatedEntityInterface) {
		    $this->validateProperty(DefaultsDisabledFieldInterface::PROP_DEFAULTS_DISABLED);
		}
		return $this;
	}
}
