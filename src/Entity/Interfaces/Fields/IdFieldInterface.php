<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Fields;

interface IdFieldInterface
{
    public const PROPERTY_NAME= 'id';

    public function getId(): int;
}
