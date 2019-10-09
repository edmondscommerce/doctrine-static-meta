<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Attribute;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\AbstractEmbeddableObjectInterface;

interface WeightEmbeddableInterface extends AbstractEmbeddableObjectInterface
{
    public const EMBEDDED_PROP_UNIT = 'unit';
    public const EMBEDDED_PROP_VALUE = 'value';

    public const DEFAULT_UNIT = self::UNIT_GRAM;
    public const DEFAULT_VALUE = 0.0;

    public const DEFAULTS = [
        self::EMBEDDED_PROP_UNIT  => self::DEFAULT_UNIT,
        self::EMBEDDED_PROP_VALUE => self::DEFAULT_VALUE,
    ];

    /**
     * Imperial
     */
    public const UNIT_GRAIN         = 'grain';
    public const UNIT_DRAM          = 'dram';
    public const UNIT_OUNCE         = 'ounce';
    public const UNIT_POUND         = 'pound';
    public const UNIT_HUNDREDWEIGHT = 'hundredweight';
    public const UNIT_TON           = 'ton';
    /**
     * Troy
     */
    public const UNIT_TROY_GRAIN       = 'troy grain';
    public const UNIT_TROY_PENNYWEIGHT = 'pennyweight';
    public const UNIT_TROY_OUNCE       = 'troy ounce';
    public const UNIT_TROY_POUND       = 'troy pound';
    /**
     * Metric
     */
    public const UNIT_MILLIGRAM  = 'milligram';
    public const UNIT_CENTIGRAM  = 'centigram';
    public const UNIT_GRAM       = 'gram';
    public const UNIT_DEKAGRAM   = 'dekagram';
    public const UNIT_HECTOGRAM  = 'hectogram';
    public const UNIT_KILOGRAM   = 'kilogram';
    public const UNIT_TONNE      = 'tonne';
    public const UNIT_METRIC_TON = self::UNIT_TONNE;

    public const VALID_UNITS = [
        self::UNIT_GRAIN,
        self::UNIT_DRAM,
        self::UNIT_OUNCE,
        self::UNIT_POUND,
        self::UNIT_HUNDREDWEIGHT,
        self::UNIT_TON,
        self::UNIT_TROY_GRAIN,
        self::UNIT_TROY_PENNYWEIGHT,
        self::UNIT_TROY_OUNCE,
        self::UNIT_TROY_POUND,
        self::UNIT_MILLIGRAM,
        self::UNIT_CENTIGRAM,
        self::UNIT_GRAM,
        self::UNIT_DEKAGRAM,
        self::UNIT_HECTOGRAM,
        self::UNIT_KILOGRAM,
        self::UNIT_TONNE,
    ];


    public function getUnit(): string;

    public function getValue(): float;
}
