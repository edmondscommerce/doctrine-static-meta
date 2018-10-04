<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

interface ValidatedEntityInterface
{
    /**
     * Protected static methods starting wth this prefix will be used to load validator meta data
     */
    public const METHOD_PREFIX_GET_PROPERTY_VALIDATOR_META = 'validatorMetaFor';

    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void;

    public function injectValidator(EntityValidatorFactory $factory);

    public function isValid(): bool;

    public function validate();

    public function validateProperty(string $propertyName);
}
