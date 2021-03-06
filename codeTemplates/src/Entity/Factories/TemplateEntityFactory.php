<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Factories;

// phpcs:disable -- line length
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entity\DataTransferObjects\TemplateEntityDto;
use TemplateNamespace\Entity\Factories\AbstractEntityFactory as DsmAbstractEntityFactory;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

// phpcs: enable
class TemplateEntityFactory extends DsmAbstractEntityFactory
{
    public function create(TemplateEntityDto $dto = null): TemplateEntityInterface
    {
        return $this->entityFactory->create(TemplateEntity::class, $dto);
    }
}
