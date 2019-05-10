<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Binary;

use Ramsey\Uuid\UuidInterface;

interface BinaryUuidFieldInterface
{
    public const PROP_BINARY_UUID = 'binaryUuid';

    public const DEFAULT_BINARY_UUID = null;

    public function getBinaryUuid(): ?UuidInterface;
}
