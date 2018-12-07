<?php

namespace TemplateNamespace\Entity\Savers;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\DataTransferObjectInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\EntitySaver;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\NewUpsertDtoDataModifierInterface;
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
     * @var TemplateEntityFactory
     */
    private $entityFactory;
    /**
     * @var TemplateEntityRepository
     */
    private $repository;
    /**
     * @var EntitySaver
     */
    private $saver;
    /**
     * @var TemplateEntityUnitOfWorkHelper
     */
    private $unitOfWorkHelper;

    public function __construct(
        TemplateEntityRepository $repository,
        TemplateEntityDtoFactory $dtoFactory,
        TemplateEntityFactory $entityFactory,
        EntitySaver $saver,
        TemplateEntityUnitOfWorkHelper $unitOfWorkHelper
    ) {
        $this->repository       = $repository;
        $this->dtoFactory       = $dtoFactory;
        $this->entityFactory    = $entityFactory;
        $this->saver            = $saver;
        $this->unitOfWorkHelper = $unitOfWorkHelper;
    }

    /**
     * This method is used to get a DTO using search criteria, when you are not certain if the entity exists or not.
     * The criteria is passed through to the repository findOneBy method, if an entity is found then a DTO will be
     * created from it and returned.
     *
     * If an entity is not found then a new empty DTO will be created and returned instead.
     *
     * @param array                             $criteria
     * @param NewUpsertDtoDataModifierInterface $modifier
     *
     * @return TemplateEntityDto
     * @see \Doctrine\ORM\EntityRepository::findOneBy for how to use the crietia
     */
    public function getUpsertDtoByCriteria(
        array $criteria,
        NewUpsertDtoDataModifierInterface $modifier
    ): TemplateEntityDto {
        $entity = $this->repository->findOneBy($criteria);
        if ($entity === null) {
            $dto = $this->dtoFactory->create();
            $modifier->addDataToNewlyCreatedDto($dto);

            return $dto;
        }

        $this->unitOfWorkHelper->addEntityRecord($entity);

        return $this->dtoFactory->createDtoFromTemplateEntity($entity);
    }

    public function getUpsertDtoByProperties(array $propertiesToValues): TemplateEntityDto
    {
        $modifier = $this->getModifierClass($propertiesToValues);

        return $this->getUpsertDtoByCriteria($propertiesToValues, $modifier);
    }

    public function getUpsertDtoByProperty(string $propertyName, $value): TemplateEntityDto
    {
        $modifier = $this->getModifierClass([$propertyName => $value]);

        return $this->getUpsertDtoByCriteria([$propertyName => $value], $modifier);
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
        if ($this->unitOfWorkHelper->hasRecordOfDto($dto) === false) {
            $entity = $this->entityFactory->create($dto);
            $this->saver->save($entity);
            $this->unitOfWorkHelper->addEntityRecord($entity);


            return $entity;
        }
        $entity = $this->unitOfWorkHelper->getEntityFromUnitOfWorkUsingDto($dto);
        $entity->update($dto);
        $this->saver->save($entity);

        return $entity;
    }

    private function getModifierClass(array $propertiesToValues): NewUpsertDtoDataModifierInterface
    {
        return new class($propertiesToValues) implements NewUpsertDtoDataModifierInterface
        {
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
    }
}
