<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey;

interface UuidFieldInterface
{
    public const PROPERTY_NAME= 'uuid';

    public function getId(): string;
}
