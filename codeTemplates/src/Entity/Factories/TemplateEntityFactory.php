<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Factories;

use FQNFor\AbstractEntityFactory;
use EntityFqn;
use TemplateNamespace\Entity\Interfaces;

// phpcs:disable -- line length
class TemplateEntityFactory extends AbstractEntityFactory
{
// phpcs: enable

    public function create(array $values = []): TemplateEntityInterface
    {
        return $this->entityFactory->create(TemplateEntity::class, $values);
    }
}
