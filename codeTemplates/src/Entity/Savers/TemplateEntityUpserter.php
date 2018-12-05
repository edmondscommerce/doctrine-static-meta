<?php

namespace TemplateNamespace\Entity\Savers;

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
        TemplateEntitySaver $saver
    ) {
        $this->repository    = $repository;
        $this->dtoFactory    = $dtoFactory;
        $this->entityFactory = $entityFactory;
        $this->saver         = $saver;
    }

    public function getUpsertDtoByCriteria(array $criteria): TemplateEntityDto
    {
        $entity = $this->repository->findOneBy($criteria);
        if ($entity === null) {
            $dto = $this->dtoFactory->create();
            $this->addDataToNewlyCreatedDto($dto);

            return $dto;
        }

        if (!$entity instanceof TemplateEntity) {
            throw new \LogicException('We still need to choose between interfaces and concretions');
        }

        $key                  = $this->getKeyForEntity($entity);
        $this->entities[$key] = $entity;

        return $this->dtoFactory->createDtoFromTemplateEntity($entity);
    }

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
     * @param TemplateEntity $entity
     *
     * @return string
     * @see getKeyForDto
     */
    protected function getKeyForEntity(TemplateEntity $entity): string
    {
        return $entity->getId()->toString();
    }
}
