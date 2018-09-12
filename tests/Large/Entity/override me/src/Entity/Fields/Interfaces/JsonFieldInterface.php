<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces;

interface JsonFieldInterface
{
    public const PROP_JSON = 'json';

    public const DEFAULT_JSON = null;

    public function getJson(): ?string;

    public function setJson(?string $json);
}
