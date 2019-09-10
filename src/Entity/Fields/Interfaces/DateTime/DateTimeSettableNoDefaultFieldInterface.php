<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\DateTime;

use DateTimeImmutable;

interface DateTimeSettableNoDefaultFieldInterface
{
    public const PROP_DATE_TIME_SETTABLE_NO_DEFAULT = 'dateTimeSettableNoDefault';

    public const DEFAULT_DATE_TIME_SETTABLE_NO_DEFAULT = null;

    public function getDateTimeSettableNoDefault(): ?DateTimeImmutable;
}
