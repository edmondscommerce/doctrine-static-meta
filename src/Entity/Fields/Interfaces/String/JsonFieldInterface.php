<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface JsonFieldInterface
{
    public const PROP_JSON = 'json';

    public const DEFAULT_JSON = null;

    public function getJson(): ?string;
}
