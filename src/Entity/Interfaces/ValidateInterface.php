<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

interface ValidateInterface
{
    /**
     * The public static method that is called to load the validator meta data
     *
     * @see https://symfony.com/doc/current/components/validator/resources.html#the-staticmethodloader
     *
     * Debugging has shown that the above is not true, it's calling `getPropertyValidatorMetaFor`. - Need to clean this
     * up
     */
    public const METHOD_LOAD_VALIDATOR_META_DATA = 'loadValidatorMetadata';

    /**
     * Protected static methods starting wth this prefix will be used to load validator meta data
     */
    public const METHOD_PREFIX_GET_PROPERTY_VALIDATOR_META = 'getPropertyValidatorMetaFor';

    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void;

    public function setValidated();

    public function setNeedsValidating();

    public function needsValidating(): bool;
}
