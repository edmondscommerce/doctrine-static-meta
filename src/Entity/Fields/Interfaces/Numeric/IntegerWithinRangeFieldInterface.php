<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric;

interface IntegerWithinRangeFieldInterface
{
    public const PROP_INTEGER_WITHIN_RANGE = 'integerWithinRange';

    public const DEFAULT_INTEGER_WITHIN_RANGE = null;

    public const MIN_INTEGER_WITHIN_RANGE = 0;
    public const MAX_INTEGER_WITHIN_RANGE = 100;
    public const MIN_MESSAGE_INTEGER_WITHIN_RANGE = self::PROP_INTEGER_WITHIN_RANGE . ' must at least {{ limit }}';
    public const MAX_MESSAGE_INTEGER_WITHIN_RANGE = self::PROP_INTEGER_WITHIN_RANGE . ' must at most {{ limit }}';

    public function getIntegerWithinRange(): ?int;
}
