<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsNullFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait DefaultsNullFieldTrait
{

    /**
     * @var bool|null
     */
    private $defaultsNull;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDefaultsNull(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleBooleanFields(
            [DefaultsNullFieldInterface::PROP_DEFAULTS_NULL],
            $builder,
            DefaultsNullFieldInterface::DEFAULT_DEFAULTS_NULL
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
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForDefaultsNull(ValidatorClassMetaData $metadata)
    {
        //        $metadata->addPropertyConstraint(
        //            DefaultsNullFieldInterface::PROP_DEFAULTS_NULL,
        //            new NotBlank()
        //        );
    }

    /**
     * @return bool|null
     */
    public function isDefaultsNull(): ?bool
    {
        return $this->defaultsNull;
    }

    /**
     * @param bool|null $defaultsNull
     *
     * @return self
     */
    public function setDefaultsNull(?bool $defaultsNull): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            DefaultsNullFieldInterface::PROP_DEFAULTS_NULL,
            $defaultsNull
        );

        return $this;
    }
}
