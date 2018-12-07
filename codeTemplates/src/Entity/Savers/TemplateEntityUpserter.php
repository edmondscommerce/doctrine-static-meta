<?php

namespace TemplateNamespace\Entity\Savers;

use Doctrine\ORM\EntityManagerInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity as DSM;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entity\DataTransferObjects\TemplateEntityDto;
use TemplateNamespace\Entity\Factories\TemplateEntityDtoFactory;
use TemplateNamespace\Entity\Factories\TemplateEntityFactory;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Repositories\TemplateEntityRepository;

class TemplateEntityUpserter
{
    /**
     * @var TemplateEntityDtoFactory
     */
    private $dtoFactory;
    /**
     * @var array
     */
    private $entities = [];
    /**
     * @var TemplateEntityFactory
     */
    private $entityFactory;
    /**
     * @var TemplateEntityRepository
     */
    private $repository;
    /**
     * @var TemplateEntitySaver
     */
    private $saver;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        TemplateEntityRepository $repository,
        TemplateEntityDtoFactory $dtoFactory,
        TemplateEntityFactory $entityFactory,
        DSM\Savers\EntitySaver $saver,
        EntityManagerInterface $entityManager
    ) {
        $this->repository    = $repository;
        $this->dtoFactory    = $dtoFactory;
        $this->entityFactory = $entityFactory;
        $this->saver         = $saver;
        $this->entityManager = $entityManager;
    }

    public function getUpsertDtoByProperty(string $propertyName, $value): TemplateEntityDto
    {
        $modifier = new class($propertyName, $value) implements DSM\Savers\NewUpsertDtoDataModifierInterface {

            private $propertyName;
            private $value;

            public function __construct(string $propertyName, $value)
            {
                $this->propertyName = $propertyName;
                $this->value = $value;
            }

            public function addDataToNewlyCreatedDto(DataTransferObjectInterface $dto): void
            {
                $setter = 'set' . ucfirst($this->propertyName);
                $dto->$setter($this->value);
            }
        };

        return $this->getUpsertDtoByCriteria([$propertyName => $value], $modifier);
    }

    public function getUpsertDtoByProperties(array $propertiesToValues): TemplateEntityDto
    {
        $modifier = new class($propertiesToValues) implements DSM\Savers\NewUpsertDtoDataModifierInterface
        {
            /**
             * @var array
             */
            private $propertiesToValues;

            public function __construct(array $propertiesToValues)
            {
                $this->propertiesToValues = $propertiesToValues;
            }

            public function addDataToNewlyCreatedDto(DataTransferObjectInterface $dto): void
            {
                foreach ($this->propertiesToValues as $property => $value) {
                    $setter = 'set' . ucfirst($property);
                    $dto->$setter($value);
                }
            }
        };

        return $this->getUpsertDtoByCriteria($propertiesToValues, $modifier);
    }

    /**
     * This method is used to get a DTO using search criteria, when you are not certain if the entity exists or not.
     * The criteria is passed through to the repository findOneBy method, if an entity is found then a DTO will be
     * created from it and returned.
     *
     * If an entity is not found then a new empty DTO will be created and returned instead.
     *
     * @param array $criteria
     *
     * @return TemplateEntityDto
     * @see \Doctrine\ORM\EntityRepository::findOneBy for how to use the crietia
     */
    public function getUpsertDtoByCriteria(array $criteria, DSM\Savers\NewUpsertDtoDataModifierInterface $modifier): TemplateEntityDto
    {
        $entity = $this->repository->findOneBy($criteria);
        if ($entity === null) {
            $dto = $this->dtoFactory->create();
            $modifier->addDataToNewlyCreatedDto($dto);

            return $dto;
        }

        $idHash = spl_object_hash($entity);

        $key                  = $this->getKeyForEntity($entity);
        $this->entities[$key] = $idHash;

        if (!$entity instanceof TemplateEntity) {
            throw new \LogicException('We still need to choose between interfaces and concretions');
        }

        return $this->dtoFactory->createDtoFromTemplateEntity($entity);
    }

    /**
     * This is used to persist the DTO to the database. If the DTO is for a new entity then it will be created, if it
     * is for an existing Entity then it will be updated.
     *
     * Be aware that this method should __only__ be used with DTOs that have been created using the
     * self::getUpsertDtoByCriteria method, as if they come from elsewhere we will not not if the entity needs to be
     * created or updated
     *
     * @param TemplateEntityDto $dto
     *
     * @return TemplateEntityInterface
     */
    public function persistUpsertDto(TemplateEntityDto $dto): TemplateEntityInterface
    {
        $key = $this->getKeyForDto($dto);
        if (!isset($this->entities[$key])) {
            $entity = $this->entityFactory->create($dto);
            $this->saver->save($entity);

            $this->entities[$key] = spl_object_hash($entity);

            return $entity;
        }
        $entity = $this->entityManager->getUnitOfWork()->getByIdHash($this->entities[$key], $dto::getEntityFqn());
        $entityKey = $this->getKeyForEntity($entity);
        if($entityKey !== $key) {
            throw new \LogicException('The entity in the Unit of Work does not match the DTO');
        }
        $entity->update($dto);
        $this->saver->save($entity);

        return $entity;
    }

    /**
     * This method is called after a new DTO is created. If the DTO should have any data set by default, e.g. Created at
     * then you can use the overrides to update this method to do that
     *
     * @param TemplateEntityDto $dto
     */
    private function addDataToNewlyCreatedDto(TemplateEntityDto $dto): void
    {
        /* Here you can add any information to the DTO that should be there */
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
}
