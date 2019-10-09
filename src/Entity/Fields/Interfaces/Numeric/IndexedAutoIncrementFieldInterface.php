<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric;

interface IndexedAutoIncrementFieldInterface
{
    public const PROP_INDEXED_AUTO_INCREMENT = 'indexedAutoIncrement';

    public const DEFAULT_INDEXED_AUTO_INCREMENT = null;

    public function getIndexedAutoIncrement(): ?int;
}
