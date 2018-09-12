<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface BooleanFieldInterface
{
    public const PROP_BOOLEAN = 'boolean';

    public const DEFAULT_BOOLEAN = null;

    public function isBoolean(): ?bool;

    public function setBoolean(?bool $boolean);
}
