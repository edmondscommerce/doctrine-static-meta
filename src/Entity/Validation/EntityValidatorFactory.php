<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation;

use Doctrine\Common\Cache\Cache;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;
use Symfony\Component\Validator\Validation;

class EntityValidatorFactory
{
    /**
     * @var Cache
     */
    protected $doctrineCacheDriver;

    /**
     * ValidatorFactory constructor.
     *
     * You need to specify the cache driver implementation at the DI level
     *
     * @param Cache $doctrineCacheDriver
     */
    public function __construct(Cache $doctrineCacheDriver)
    {
        $this->doctrineCacheDriver = $doctrineCacheDriver;
    }

    /**
     * @param null|CacheInterface $cache
     *
     * @return EntityValidator
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getEntityValidator(?CacheInterface $cache = null): EntityValidator
    {
        $builder = Validation::createValidatorBuilder();
        $builder->addMethodMapping(ValidateInterface::METHOD_PREFIX_GET_PROPERTY_VALIDATOR_META);
        if (null === $cache) {
            $cache = $this->getValidatorCache();
        }
        $builder->setMetadataCache($cache);

        $validator       = $builder->getValidator();
        $entityValidator = new EntityValidator();
        $entityValidator->setValidator($validator);

        return $entityValidator;
    }

    public function getValidatorForEntity(
        ValidateInterface $entity,
        ?CacheInterface $cache = null
    ): EntityValidator {
        $entityValidator = $this->getEntityValidator($cache);
        $entityValidator->setEntity($entity);

        return $entityValidator;
    }

    public function getValidatorCache(): CacheInterface
    {
        return new DoctrineCache($this->doctrineCacheDriver);
    }
}
