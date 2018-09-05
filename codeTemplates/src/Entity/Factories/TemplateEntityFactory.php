<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Factories;

use FQNFor\AbstractEntityFactory;
use EntityFqn;
use TemplateNamespace\Entity\Interfaces;

// phpcs:disable -- line length
class TemplateEntityFactory extends AbstractEntityFactory
{
// phpcs: enable

    public function createTemplateEntity(array $values = []): TemplateEntityInterface
    {
        return parent::create(TemplateEntity::class, $values);
    }
}
