<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface StringFieldInterface
{
    public const PROP_STRING = 'string';

    public const DEFAULT_STRING = null;

    public function getString(): ?string;

    public function setString(?string $string);
}
