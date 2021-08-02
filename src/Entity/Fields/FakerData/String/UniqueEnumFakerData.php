<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueEnumFieldInterface;

class UniqueEnumFakerData extends AbstractFakerDataProvider
{
    private static ?int $nextKey;

    public static function resetNextKey(): void
    {
        self::$nextKey = null;
    }

    public function __invoke()
    {
        $minKey = 0;
        $maxKey = count(UniqueEnumFieldInterface::UNIQUE_ENUM_OPTIONS) - 1;
        if (null === self::$nextKey) {
            self::$nextKey = $minKey;
        }
        $key = self::$nextKey;
        if ($key > $maxKey) {
            throw new \RuntimeException('Ran out of unique enum keys when trying to get an option at index ' . $key);
        }
        self::$nextKey++;

        return UniqueEnumFieldInterface::UNIQUE_ENUM_OPTIONS[$key];
    }
}
