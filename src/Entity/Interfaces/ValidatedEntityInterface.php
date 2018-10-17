<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

/**
 * A validated Entity has only one job - to provide the static meta data with regards to validation
 *
 * The meta data can be used to validate the Entity itself and also the Entity's DataTransferObject
 *
 * The actual validation work itself is performed by the EntityDataValidator
 *
 * The way meta data is loaded is by looking for static methods in the Entity beginning with one of the specified
 * method prefixes. These methods are then called and can update the validation meta data accordingly.
 *
 * @see     EntityDataValidatorInterface
 * @see     EntityDataValidator
 */
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

    /**
     * This method is called by the Symfony Validator Component when building a Validator for this Entity
     *
     * @param ValidatorClassMetaData $metadata
     */
    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void;
}
