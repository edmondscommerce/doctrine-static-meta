<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface FloatFieldInterface
{
    public const PROP_FLOAT = 'float';

    public const DEFAULT_FLOAT = null;

    public function getFloat(): ?float;

    public function setFloat(?float $float);
}
