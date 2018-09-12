<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface TextFieldInterface
{
    public const PROP_TEXT = 'text';

    public const DEFAULT_TEXT = null;

    public function getText(): ?string;

    public function setText(?string $text);
}
