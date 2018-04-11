<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Event\OnFlushEventArgs;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidator;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

class SaverValidationListener
{
    /**
     * @var EntityValidator
     */
    protected $validator;

    /**
     * @return EntityValidator
     */
    protected function getValidator(): EntityValidator
    {
        if (null === $this->validator) {
            $factory = new EntityValidatorFactory(new ArrayCache());
            $this->validator = $factory->getEntityValidator();
        }

        return $this->validator;
    }

    /**
     * @param IdFieldInterface $entity
     * @throws ValidationException
     */
    protected function validateEntity(IdFieldInterface $entity): void
    {
        if (! $entity instanceof ValidateInterface) {
            return;
        }

        if (! $entity->needsValidating()) {
            return;
        }

        $errors = $this->getValidator()
            ->setEntity($entity)
            ->validate();

        if (0 === $errors->count()) {
            $entity->setValidated();
            return;
        }

        throw new ValidationException((string) $errors, $errors, $entity);
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     * @throws ValidationException
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $unitOfWork    = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $this->validateEntity($entity);
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $this->validateEntity($entity);
        }

        foreach ($unitOfWork->getScheduledCollectionUpdates() as $entities) {
            foreach ($entities as $entity) {
                $this->validateEntity($entity);
            }
        }
    }
}