<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidateTrait
{

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws DoctrineStaticMetaException
     */
    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void
    {
        static::$reflectionClass = $metadata->getReflectionClass();
        static::loadPropertyValidatorMetaData($metadata);
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws DoctrineStaticMetaException
     */
    protected static function loadPropertyValidatorMetaData(ValidatorClassMetaData $metadata): void
    {
        $methodName = '__no_method__';
        try {
            $staticMethods = static::getStaticMethods();
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if (0 === stripos($methodName, ValidateInterface::METHOD_PREFIX_GET_PROPERTY_VALIDATOR_META)) {
                    static::$methodName($metadata);
                }
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in '.__METHOD__.'for '
                .self::$reflectionClass->getName()."::$methodName\n\n"
                .$e->getMessage()
            );
        }
    }
}
