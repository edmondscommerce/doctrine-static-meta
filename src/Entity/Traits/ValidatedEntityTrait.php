<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Factory\EntityDependencyInjector;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\ValidatorStaticMeta;
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
     * @var ValidatorStaticMeta|null
     */
    private static $validatorStaticMeta;
    /**
     * @var EntityValidatorInterface
     */
    protected $validator;

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


    private static function getValidatorStaticMeta(): ValidatorStaticMeta
    {
        if (null === self::$validatorStaticMeta) {
            self::$validatorStaticMeta = new ValidatorStaticMeta(self::getDoctrineStaticMeta());
        }

        return self::$validatorStaticMeta;
    }

    /**
     * Called as part of the Entity Dependency Injection in the Entity Factory
     *
     * @see EntityDependencyInjector
     *
     * @param EntityValidatorFactory $factory
     */
    public function injectValidatorFactory(EntityValidatorFactory $factory): void
    {
        $this->validator = $factory->getEntityValidator();
        $this->validator->setEntity($this);
    }


    private function getValidator(): EntityValidatorInterface
    {
        if (!$this->validator instanceof EntityValidatorInterface) {
            throw new \RuntimeException(
                'The validator has not been set. It should be an instance of EntityValidatorInterface. You must call injectValidatorFactory'
            );
        }

        return $this->validator;
    }

}
