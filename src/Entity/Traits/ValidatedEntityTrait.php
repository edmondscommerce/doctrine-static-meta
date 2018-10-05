<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\ValidatorStaticMeta;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

/**
 * @see ValidatedEntityInterface
 */
trait ValidatedEntityTrait
{
    /**
     * @var ValidatorStaticMeta|null
     */
    private static $validatorStaticMeta;

    /**
     * This method is called by the Symfony validation component when loading the meta data
     *
     * In this method, we pass around the meta data object and add data to it as required.
     *
     *
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws DoctrineStaticMetaException
     */
    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void
    {
        static::getValidatorStaticMeta()->addValidatorMetaData($metadata);
    }


    /**
     * Get an instance of the ValidatorStaticMeta object for this Entity
     *
     * @return ValidatorStaticMeta
     */
    private static function getValidatorStaticMeta(): ValidatorStaticMeta
    {
        if (null === self::$validatorStaticMeta) {
            self::$validatorStaticMeta = new ValidatorStaticMeta(self::getDoctrineStaticMeta());
        }

        return self::$validatorStaticMeta;
    }
}
