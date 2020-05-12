<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueEnumFieldInterface;

class UniqueEnumFakerData extends AbstractFakerDataProvider
{
    private static $currentKey = 0;

    public function __invoke()
    {
        if (array_key_exists(self::$currentKey, UniqueEnumFieldInterface::UNIQUE_ENUM_OPTIONS)) {
            return UniqueEnumFieldInterface::UNIQUE_ENUM_OPTIONS[self::$currentKey++];
        }
        throw new \RuntimeException('Ran out of unique enum keys to fake');
    }
}
