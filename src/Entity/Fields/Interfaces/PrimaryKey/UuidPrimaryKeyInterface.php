<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Factories\UuidFactory;
use Ramsey\Uuid\UuidInterface;

interface UuidPrimaryKeyInterface
{
    public static function buildUuid(UuidFactory $factory): UuidInterface;

    public function getUuid(): UuidInterface;
}
