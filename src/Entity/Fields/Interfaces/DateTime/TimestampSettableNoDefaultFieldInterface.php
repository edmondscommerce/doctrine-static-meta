<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime;

interface TimestampSettableNoDefaultFieldInterface
{
    public const PROP_TIMESTAMP_SETTABLE_NO_DEFAULT = 'timestampSettableNoDefault';

    public const DEFAULT_TIMESTAMP_SETTABLE_NO_DEFAULT = null;

    public function getTimestampSettableNoDefault(): ?\DateTime;

    public function setTimestampSettableNoDefault(?\DateTime $timestampSettableNoDefault);
}
