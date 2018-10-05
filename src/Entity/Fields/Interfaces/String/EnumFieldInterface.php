<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface EnumFieldInterface
{
    public const PROP_ENUM = 'enum';

    public const ENUM_OPTION_FOO = 'foo';
    public const ENUM_OPTION_BAR = 'bar';
    public const ENUM_OPTIONS    = [
        self::ENUM_OPTION_FOO,
        self::ENUM_OPTION_BAR,
    ];

    public const DEFAULT_ENUM = self::ENUM_OPTION_FOO;

    public function getEnum(): string;
}
