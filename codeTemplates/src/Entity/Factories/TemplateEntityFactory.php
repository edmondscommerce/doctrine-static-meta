<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Factories;
// phpcs:disable -- line length
use FQNFor\AbstractEntityFactory;
use EntityFqn;
use TemplateNamespace\Entity\Interfaces;
// phpcs: enable
class TemplateEntityFactory extends AbstractEntityFactory
{
    public function create(array $values = []): TemplateEntityInterface
    {
        return $this->entityFactory->create(TemplateEntity::class, $values);
    }
}
