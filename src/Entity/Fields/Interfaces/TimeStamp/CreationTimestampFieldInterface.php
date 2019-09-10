<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\TimeStamp;

use DateTimeImmutable;

interface CreationTimestampFieldInterface
{
    public const PROP_CREATION_TIMESTAMP = 'creationTimestamp';

    public const DEFAULT_CREATION_TIMESTAMP = null;

    public function getCreationTimestamp(): ?DateTimeImmutable;
}
