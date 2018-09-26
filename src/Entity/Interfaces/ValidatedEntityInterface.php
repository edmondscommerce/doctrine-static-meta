<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

interface ValidatedEntityInterface
{
    /**
     * Protected static methods starting wth this prefix will be used to load validator meta data. They should be for
     * individual property level validation
     */
    public const METHOD_PREFIX_PROPERTY_VALIDATOR_META = 'validatorMetaForProperty';

    /**
     * Protected static methods starting with this prefix will be used to load validator meta data. They should be for
     * Entity as a whole level validation
     */
    public const METHOD_PREFIX_ENTITY_VALIDATOR_META = 'validatorMetaForEntity';

    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void;

    public function injectValidator(EntityValidatorFactory $factory);

    public function isValid(): bool;

    public function validate();

    public function validateProperty(string $propertyName);
}
