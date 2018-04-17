<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

interface ValidatedEntityInterface
{
    /**
     * Protected static methods starting wth this prefix will be used to load validator meta data
     */
    public const METHOD_PREFIX_GET_PROPERTY_VALIDATOR_META = 'getPropertyValidatorMetaFor';

    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void;

    public function isValid(): bool;

    public function validate();

    public function validateProperty(string $propertyName);
}
