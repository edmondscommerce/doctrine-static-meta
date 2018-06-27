<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface UniqueStringFieldInterface
{
    public const PROP_UNIQUE_STRING = 'uniqueString';

    public const DEFAULT_UNIQUE_STRING = 'NOT SET';

    public function getUniqueString(): ?string;

    public function setUniqueString(?string $uniqueString);
}
