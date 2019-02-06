<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric;

interface IndexedUniqueIntegerFieldInterface
{
    public const PROP_INDEXED_UNIQUE_INTEGER = 'indexedUniqueInteger';

    public const DEFAULT_INDEXED_UNIQUE_INTEGER = 0;

    public function getIndexedUniqueInteger(): int;
}
