<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric;

interface FloatWithinRangeFieldInterface
{
    public const PROP_FLOAT_WITHIN_RANGE = 'floatWithinRange';

    public const DEFAULT_FLOAT_WITHIN_RANGE = null;

    public const MIN_FLOAT_WITHIN_RANGE = 0.0;
    public const MAX_FLOAT_WITHIN_RANGE = 100.0;
    public const MIN_MESSAGE_FLOAT_WITHIN_RANGE = self::PROP_FLOAT_WITHIN_RANGE . ' must at least {{ limit }}';
    public const MAX_MESSAGE_FLOAT_WITHIN_RANGE = self::PROP_FLOAT_WITHIN_RANGE . ' must at most {{ limit }}';

    public function getFloatWithinRange(): ?float;
}
