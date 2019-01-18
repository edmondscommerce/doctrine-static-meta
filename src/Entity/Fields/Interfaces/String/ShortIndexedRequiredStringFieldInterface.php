<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface ShortIndexedRequiredStringFieldInterface
{
    public const PROP_SHORT_INDEXED_REQUIRED_STRING = 'shortIndexedRequiredString';

    public const DEFAULT_SHORT_INDEXED_REQUIRED_STRING = 'NO VALUE SET';

    public const LENGTH_SHORT_INDEXED_REQUIRED_STRING = 50;

    public function getShortIndexedRequiredString(): string;
}
