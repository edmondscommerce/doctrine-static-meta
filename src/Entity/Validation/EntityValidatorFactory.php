<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Validation;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Validation\EntityValidatorInterface;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;
use Symfony\Component\Validator\Validation;

class EntityValidatorFactory
{
    /**
     * The public static method that is called to load the validator meta data
     *
     * @see https://symfony.com/doc/current/validation.html
     *
     * @see https://symfony.com/doc/current/components/validator/resources.html#the-staticmethodloader
     *
     */
    public const METHOD_LOAD_VALIDATOR_META_DATA = 'loadValidatorMetadata';

    /**
     * @var DoctrineCache
     */
    protected $doctrineCache;

    /**
     * ValidatorFactory constructor.
     *
     * You need to specify the cache driver implementation at the DI level
     *
     * @param DoctrineCache $doctrineCache
     */
    public function __construct(DoctrineCache $doctrineCache)
    {
        $this->doctrineCache = $doctrineCache;
    }

    /**
     * @return EntityValidatorInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getEntityValidator(): EntityValidatorInterface
    {
        $builder = Validation::createValidatorBuilder();
        $builder->addMethodMapping(self::METHOD_LOAD_VALIDATOR_META_DATA);
        $builder->setMetadataCache($this->doctrineCache);
        $validator       = $builder->getValidator();
        $entityValidator = new EntityValidator();
        $entityValidator->setValidator($validator);

        return $entityValidator;
    }
}
