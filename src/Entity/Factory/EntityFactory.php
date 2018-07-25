<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Factory;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;

class EntityFactory
{
    /**
     * @var EntityValidatorFactory
     */
    protected $entityValidatorFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityValidatorFactory $entityValidatorFactory, EntityManagerInterface $entityManager)
    {
        $this->entityValidatorFactory = $entityValidatorFactory;
        $this->entityManager          = $entityManager;
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
        $this->addListenerToEntityIfRequired($entity);
        foreach ($values as $property => $value) {
            $setter = 'set' . $property;
            if (!method_exists($entity, $setter)) {
                throw new \InvalidArgumentException(
                    'The entity ' . $entityFqn . ' does not have the setter method ' . $setter
                );
            }
            $entity->$setter($value);
        }

        return $entity;
    }

    private function addListenerToEntityIfRequired($entity): void
    {
        if (!$entity instanceof NotifyPropertyChanged) {
            return;
        }
        $listener = $this->entityManager->getUnitOfWork();
        $entity->addPropertyChangedListener($listener);
    }
}
