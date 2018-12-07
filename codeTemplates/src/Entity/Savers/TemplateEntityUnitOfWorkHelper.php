<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use TemplateNamespace\Entity\DataTransferObjects\TemplateEntityDto;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

class TemplateEntityUnitOfWorkHelper
{

    private $entities = [];
    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->unitOfWork = $entityManager->getUnitOfWork();
    }

    /**
     *
     *
     * @param TemplateEntityInterface $entity
     */
    public function addEntityRecord(TemplateEntityInterface $entity): void
    {
        if ($this->unitOfWork->isInIdentityMap($entity) === false) {
            throw new \LogicException('The Entity is not managed by the Unit of Work');
        }
        $key                  = $this->getKeyForEntity($entity);
        $identifier           = $this->getUnitOfWorkIdentifier($entity);
        $this->entities[$key] = $identifier;
    }

    public function getEntityFromUnitOfWorkUsingDto(TemplateEntityDto $dto): TemplateEntityInterface
    {
        if ($this->hasRecordOfDto($dto) === false) {
            throw new \LogicException('Trying to fetch an entity we don\'t know about');
        }
        $key = $this->getKeyForDto($dto);
        $entityName = $dto::getEntityFqn();
        $entity = $this->unitOfWork->tryGetById($this->entities[$key], $entityName);
        if ($entity === false) {
            throw new \LogicException('Entity is unknown in the unit of work');
        }
        if (!$entity instanceof TemplateEntityInterface) {
            throw new \LogicException('Unknown class returned from the unit of work, got ' . get_class($entity));
        }
        $entityKey = $this->getKeyForEntity($entity);
        if ($entityKey !== $key) {
            throw new \LogicException('The entity in the Unit of Work does not match the DTO');
        }

        return $entity;
    }

    public function hasRecordOfDto(TemplateEntityDto $dto): bool
    {
        $key = $this->getKeyForDto($dto);

        return isset($this->entities[$key]);
    }

    /**
     * Each entity must by uniquely identifiable using a string. Normally we use the string representation of the UUID,
     * however if you are using something else for the ID, e.g. a Compound Key, int etc, then you can override this
     * method and generate a unique string for the DTO.
     *
     * Note that the output of this must match the output of getKeyForEntity exactly for the same DTO / Entity
     *
     * @param TemplateEntityDto $dto
     *
     * @return string
     */
    private function getKeyForDto(TemplateEntityDto $dto): string
    {
        return $dto->getId()->toString();
    }

    /**
     * @param TemplateEntityInterface $entity
     *
     * @return string
     * @see getKeyForDto
     */
    private function getKeyForEntity(TemplateEntityInterface $entity): string
    {
        return $entity->getId()->toString();
    }

    private function getUnitOfWorkIdentifier(TemplateEntityInterface $entity): array
    {
        return $this->unitOfWork->getEntityIdentifier($entity);
    }
}
