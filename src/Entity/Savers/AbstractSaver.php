<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;

abstract class AbstractSaver
{
    protected $entityManager;

    protected $entityValidator;

    public function __construct(
        EntityManagerInterface $entityManager,
        EntityValidatorFactory $entityValidatorFactory
    ) {
        $this->entityManager   = $entityManager;
        $this->entityValidator = $entityValidatorFactory->getEntityValidator();
    }

    public function save($entity): void
    {
        $this->saveAll([$entity]);
    }

    public function saveAll(array $entities): void
    {
        if (empty($entities)) {
            return;
        }

        foreach ($entities as $entity) {
            if (! $this->isCorrectEntityType($entity)) {
                // TODO need to log this.
                // TODO should this be an exception?
                continue;
            }
            /*
             * TODO What to do if invalid
             *
             * If I understand correctly I can't just not call persist and then allow the flush as pre existing
             * entities will already be tracked by doctrine and will therefore be updated when I call flush.
             *
             * How will this happen across savers. Will the state of other entities be saved when I call flush?
             */
//            $errors = $this->validateEntity($entity);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    public function remove($entity): void
    {
        $this->removeAll([$entity]);
    }

    public function removeAll(array $entities): void
    {
        foreach ($entities as $entity) {
            if (! $this->isCorrectEntityType($entity)) {
                // TODO need to log this.
                // TODO should this be an exception?
                continue;
            }
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }

    protected function isCorrectEntityType($entity): bool
    {
        $entityClassName = $this->getEntityClassName();

        return $entity instanceof $entityClassName;
    }

    protected function validateEntity($entity)
    {
        if (! $entity instanceof ValidateInterface) {
            return null;
        }

        if (! $entity->needsValidating()) {
            return null;
        }

        $errors = $this->entityValidator
            ->setEntity($entity)
            ->validate();

        if (null === $errors) {
            $entity->setValidated();
        }

        return $errors;
    }

    protected function getEntityClassName()
    {
        $saverClassName = \get_class($this);
        return substr($saverClassName, 0, -5);
    }
}
