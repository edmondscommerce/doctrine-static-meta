<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Factories;

// phpcs:disable -- line length
use EdmondsCommerce\DoctrineStaticMeta\Entity\DataTransferObjects\AbstractEntityDtoFactory;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entity\DataTransferObjects\TemplateEntityDto;

// phpcs: enable
class TemplateEntityDtoFactory extends AbstractEntityDtoFactory
{
    public function createEmptyDtoFromEntityFqn(string $entityFqn): TemplateEntityDto
    {
        return $this->dtoFactory->createEmptyDtoFromEntityFqn($entityFqn);
    }

    public function createDtoFromTemplateEntity(TemplateEntity $entity): TemplateEntityDto
    {
        return $this->dtoFactory->createDtoFromEntity($entity);
    }

}
