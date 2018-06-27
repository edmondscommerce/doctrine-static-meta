<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;

class EntityFactory
{
    /**
     * @var EntityValidatorFactory
     */
    protected $entityValidatorFactory;

    public function __construct(EntityValidatorFactory $entityValidatorFactory)
    {
        $this->entityValidatorFactory = $entityValidatorFactory;
    }

    /**
     * Build a new empty entity with the validator factory preloaded
     *
     * @param string $entityFqn
     *
     * @return EntityInterface
     */
    public function create(string $entityFqn): EntityInterface
    {
        return new $entityFqn($this->entityValidatorFactory);
    }
}
