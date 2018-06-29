<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

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
     * Build a new entity with the validator factory preloaded
     *
     * Optionally pass in an array of property=>value
     *
     * @param string $entityFqn
     *
     * @param array  $values
     *
     * @return mixed
     */
    public function create(string $entityFqn, array $values = [])
    {
        $entity = new $entityFqn($this->entityValidatorFactory);
        foreach ($values as $property => $value) {
            $setter = 'set'.$property;
            if (!method_exists($entity, $setter)) {
                throw new \InvalidArgumentException(
                    'The entity '.$entityFqn.' does not have the setter method '.$setter
                );
            }
            $entity->$setter($value);
        }

        return $entity;
    }
}
