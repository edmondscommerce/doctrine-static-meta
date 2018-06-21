<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean;
// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsEnabledFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait DefaultsEnabledFieldTrait {

	/**
	 * @var bool|null
	 */
	private $defaultsEnabled;

	/**
	 * @SuppressWarnings(PHPMD.StaticAccess) 
	 */
	public static function metaForDefaultsEnabled(ClassMetadataBuilder $builder) {
		MappingHelper::setSimpleBooleanFields(
		            [DefaultsEnabledFieldInterface::PROP_DEFAULTS_ENABLED],
		            $builder,
		            DefaultsEnabledFieldInterface::DEFAULT_DEFAULTS_ENABLED
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
	protected static function validatorMetaForDefaultsEnabled(ValidatorClassMetaData $metadata) {
		//        $metadata->addPropertyConstraint(
		//            DefaultsEnabledFieldInterface::PROP_DEFAULTS_ENABLED,
		//            new NotBlank()
		//        );
	}

	/**
	 * @return bool|null
	 */
	public function isDefaultsEnabled(): ?bool {
		if (null === $this->defaultsEnabled) {
		    return DefaultsEnabledFieldInterface::DEFAULT_DEFAULTS_ENABLED;
		}
		return $this->defaultsEnabled;
	}

	/**
	 * @param bool|null $defaultsEnabled
	 * @return self
	 */
	public function setDefaultsEnabled(?bool $defaultsEnabled): self {
		$this->defaultsEnabled = $defaultsEnabled;
		if ($this instanceof ValidatedEntityInterface) {
		    $this->validateProperty(DefaultsEnabledFieldInterface::PROP_DEFAULTS_ENABLED);
		}
		return $this;
	}
}
