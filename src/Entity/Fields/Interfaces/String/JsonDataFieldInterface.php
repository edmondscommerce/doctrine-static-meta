<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface JsonDataFieldInterface
{
    public const PROP_JSON_DATA = 'jsonData';

    public const DEFAULT_JSON_DATA = null;

    public function getJsonData(): ?string;
}
