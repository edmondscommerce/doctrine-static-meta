<?php

namespace TemplateNamespace\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
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

    public function __construct(
        TemplateEntityRepository $repository,
        TemplateEntityDtoFactory $dtoFactory,
        TemplateEntityFactory $entityFactory,
        EntitySaver $saver
    ) {
        $this->repository    = $repository;
        $this->dtoFactory    = $dtoFactory;
        $this->entityFactory = $entityFactory;
        $this->saver         = $saver;
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
    public function getUpsertDtoByCriteria(array $criteria): TemplateEntityDto
    {
        $entity = $this->repository->findOneBy($criteria);
        if ($entity === null) {
            $dto = $this->dtoFactory->create();
            $this->addDataToNewlyCreatedDto($dto);

            return $dto;
        }

        $key                  = $this->getKeyForEntity($entity);
        $this->entities[$key] = $entity;

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
            $this->entities[$key] = $this->entityFactory->create($dto);
            $this->saver->save($this->entities[$key]);

            return $this->entities[$key];
        }
        $this->entities[$key]->update($dto);
        $this->saver->save($this->entities[$key]);

        return $this->entities[$key];
    }

    /**
     * This method is called after a new DTO is created. If the DTO should have any data set by default, e.g. Created at
     * then you can update this method to do that
     *
     * @param TemplateEntityDto $dto
     */
    protected function addDataToNewlyCreatedDto(TemplateEntityDto $dto): void
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
    protected function getKeyForDto(TemplateEntityDto $dto): string
    {
        return $dto->getId()->toString();
    }

    /**
     * @param TemplateEntityInterface $entity
     *
     * @return string
     * @see getKeyForDto
     */
    protected function getKeyForEntity(TemplateEntityInterface $entity): string
    {
        return $entity->getId()->toString();
    }
}
