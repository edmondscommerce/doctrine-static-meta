<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

/**
 * Trait ValidatedEntityTrait
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Traits
 *
 * Implements ValidatedEntityInterface
 */
trait ValidatedEntityTrait
{
    /**
     * @var EntityValidatorInterface
     */
    protected $validator;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws DoctrineStaticMetaException
     */
    public static function loadValidatorMetaData(ValidatorClassMetaData $metadata): void
    {
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
            $staticMethods = self::getDoctrineStaticMeta()->getStaticMethods();
            //now loop through and call them
            foreach ($staticMethods as $method) {
                $methodName = $method->getName();
                if ($methodName === EntityInterface::METHOD_PREFIX_GET_PROPERTY_VALIDATOR_META) {
                    continue;
                }
                if (0 === stripos($methodName, EntityInterface::METHOD_PREFIX_GET_PROPERTY_VALIDATOR_META)) {
                    static::$methodName($metadata);
                }
            }
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . 'for '
                . self::class . "::$methodName\n\n"
                . $e->getMessage()
            );
        }
    }

    /**
     * Called in the Entity Constructor
     *
     * @param EntityValidatorInterface $validator
     */
    public function injectValidator(EntityValidatorInterface $validator): void
    {
        $this->validator = $validator;
        $this->validator->setEntity($this);
    }

    /**
     * Is the current Entity valid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        $validator = $this->getValidator();
        if ($validator === false) {
            return true;
        }

        return $validator->isValid();
    }

    private function getValidator()
    {
        if (null === $this->validator) {
            return false;
        }

        return $this->validator;
    }

    /**
     * Validate the current Entity
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException
     */
    public function validate(): void
    {
        $validator = $this->getValidator();
        if ($validator === false) {
            return;
        }

        $validator->validate();
    }

    /**
     * Validate a named property in the current Entity
     *
     * @param string $propertyName
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException
     */
    public function validateProperty(string $propertyName): void
    {
        $validator = $this->getValidator();
        if ($validator === false) {
            return;
        }
        $validator->validateProperty($propertyName);
    }
}
