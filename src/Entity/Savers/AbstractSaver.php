<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\IdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidateInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidatorFactory;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\EntityValidator;
use EdmondsCommerce\DoctrineStaticMeta\Exception\ValidationException;

abstract class AbstractSaver
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityValidator
     */
    protected $entityValidator;

    /**
     * @var string
     */
    protected $entityFqn;

    /**
     * AbstractSaver constructor.
     * @param EntityManagerInterface $entityManager
     * @param EntityValidatorFactory $entityValidatorFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityValidatorFactory $entityValidatorFactory
    ) {
        $this->entityManager   = $entityManager;
        $this->entityValidator = $entityValidatorFactory->getEntityValidator();
    }

    /**
     * @param IdFieldInterface $entity
     * @throws DoctrineStaticMetaException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function save(IdFieldInterface $entity): void
    {
        $this->saveAll([$entity]);
    }

    /**
     *
     *
     * @param array $entities
     * @throws DoctrineStaticMetaException
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public function saveAll(array $entities): void
    {
        if (empty($entities)) {
            return;
        }

        foreach ($entities as $entity) {
            $this->checkIsCorrectEntityType($entity);
            /*
             * TODO What to do if invalid
             *
             * If I understand correctly I can't just not call persist and then allow the flush as pre existing
             * entities will already be tracked by doctrine and will therefore be updated when I call flush.
             *
             * How will this happen across savers. Will the state of other entities be saved when I call flush?
             *
             * If I just throw the exception here then previous changes wont be flushed yet...
             */
            $this->validateEntity($entity);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param IdFieldInterface $entity
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function remove(IdFieldInterface $entity): void
    {
        $this->removeAll([$entity]);
    }

    /**
     * @param array $entities
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    public function removeAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->checkIsCorrectEntityType($entity);
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param IdFieldInterface $entity
     * @return bool
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    protected function checkIsCorrectEntityType(IdFieldInterface $entity): bool
    {
        $entityFqn = $this->getEntityFqn();

        if (! $entity instanceof $entityFqn) {
            $ref = new \ReflectionClass($entity);
            $msg = "[ {$ref->getName()} ] is not an instance of [ $entityFqn ]";
            throw new DoctrineStaticMetaException($msg);
        }
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

        $errors = $this->entityValidator
            ->setEntity($entity)
            ->validate();

        if (0 === $errors->count()) {
            $entity->setValidated();
            return;
        }

        // TODO include the invalid entity
        throw new ValidationException((string) $errors);
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function getEntityFqn()
    {
        if (null === $this->entityFqn) {
            $ref = new \ReflectionClass($this);

            $saverNamespace  = $ref->getNamespaceName();
            $entityNamespace = \str_replace(
                'Entity\\Savers',
                'Entities',
                $saverNamespace
            );

            $saverClassName  = $ref->getShortName();
            $entityClassName = substr($saverClassName, 0, -5);

            $this->entityFqn = $entityNamespace.'\\'.$entityClassName;
        }

        return $this->entityFqn;
    }
}
