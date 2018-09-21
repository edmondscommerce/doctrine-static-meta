<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Factories;

// phpcs:disable -- line length
use TemplateNamespace\Entity\Factories\AbstractEntityFactory;
use TemplateNamespace\Entities\TemplateEntity;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
// phpcs: enable
class TemplateEntityFactory extends AbstractEntityFactory
{
    public function create(array $values = []): TemplateEntityInterface
    {
        return $this->entityFactory->create(TemplateEntity::class, $values);
    }
}
