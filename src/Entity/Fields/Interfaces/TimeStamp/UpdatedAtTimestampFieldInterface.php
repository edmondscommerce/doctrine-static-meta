<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TimeStamp;

use DateTimeImmutable;

interface UpdatedAtTimestampFieldInterface
{
    public const PROP_UPDATED_AT_TIMESTAMP = 'updatedAtTimestamp';

    public const DEFAULT_UPDATED_AT_TIMESTAMP = null;

    public function getUpdatedAtTimestamp(): ?DateTimeImmutable;
}
