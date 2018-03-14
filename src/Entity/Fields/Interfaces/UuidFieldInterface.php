<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces;

interface UuidFieldInterface
{
    public const PROPERTY_NAME= 'uuid';

    public function getUuid(): string;
}
