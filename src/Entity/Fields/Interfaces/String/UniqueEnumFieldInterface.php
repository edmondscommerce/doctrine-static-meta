<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface UniqueEnumFieldInterface
{
    public const PROP_UNIQUE_ENUM = 'uniqueEnum';

    public const UNIQUE_ENUM_OPTION_FOO1 = 'foo1';
    public const UNIQUE_ENUM_OPTION_BAR1 = 'bar1';
    public const UNIQUE_ENUM_OPTION_FOO2 = 'foo2';
    public const UNIQUE_ENUM_OPTION_BAR2 = 'bar2';
    public const UNIQUE_ENUM_OPTION_FOO3 = 'foo3';
    public const UNIQUE_ENUM_OPTION_BAR3 = 'bar3';
    public const UNIQUE_ENUM_OPTION_FOO4 = 'foo4';
    public const UNIQUE_ENUM_OPTION_BAR4 = 'bar4';
    public const UNIQUE_ENUM_OPTION_FOO5 = 'foo5';
    public const UNIQUE_ENUM_OPTION_BAR5 = 'bar5';
    public const UNIQUE_ENUM_OPTION_FOO6 = 'foo6';
    public const UNIQUE_ENUM_OPTION_BAR6 = 'bar6';

    public const UNIQUE_ENUM_OPTIONS = [
        self::UNIQUE_ENUM_OPTION_FOO1,
        self::UNIQUE_ENUM_OPTION_BAR1,
        self::UNIQUE_ENUM_OPTION_FOO2,
        self::UNIQUE_ENUM_OPTION_BAR2,
        self::UNIQUE_ENUM_OPTION_FOO3,
        self::UNIQUE_ENUM_OPTION_BAR3,
        self::UNIQUE_ENUM_OPTION_FOO4,
        self::UNIQUE_ENUM_OPTION_BAR4,
        self::UNIQUE_ENUM_OPTION_FOO5,
        self::UNIQUE_ENUM_OPTION_BAR5,
        self::UNIQUE_ENUM_OPTION_FOO6,
        self::UNIQUE_ENUM_OPTION_BAR6,
    ];

    public const DEFAULT_UNIQUE_ENUM = self::UNIQUE_ENUM_OPTION_FOO1;

    public function getUniqueEnum(): string;
}
