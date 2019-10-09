<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface UrlFieldInterface
{
    public const PROP_URL = 'url';

    public const DEFAULT_URL = null;

    public function getUrl(): ?string;
}
