<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime;

interface TimestampSettableOnceFieldInterface
{
    public const PROP_TIMESTAMP_SETTABLE_ONCE = 'timestampSettableOnce';

    public const DEFAULT_TIMESTAMP_SETTABLE_ONCE = null;

    public function getTimestampSettableOnce(): ?\DateTime;

    public function setTimestampSettableOnce(?\DateTime $timestampSettableOnce);
}
