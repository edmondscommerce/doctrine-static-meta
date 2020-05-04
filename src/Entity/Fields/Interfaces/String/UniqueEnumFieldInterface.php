<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface UniqueEnumFieldInterface
{
    public const PROP_UNIQUE_ENUM = 'uniqueEnum';

    public const UNIQUE_ENUM_OPTION_FOO = 'foo';
    public const UNIQUE_ENUM_OPTION_BAR = 'bar';
    public const UNIQUE_ENUM_OPTIONS    = [
        self::UNIQUE_ENUM_OPTION_FOO,
        self::UNIQUE_ENUM_OPTION_BAR,
    ];

    public const DEFAULT_UNIQUE_ENUM = self::UNIQUE_ENUM_OPTION_FOO;

    public function getUniqueEnum(): string;
}
