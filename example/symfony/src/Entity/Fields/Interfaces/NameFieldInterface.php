<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface NameFieldInterface
{
    public const PROP_NAME = 'name';

    public function getName(): ?string;

    public function setName(?string $name);
}
