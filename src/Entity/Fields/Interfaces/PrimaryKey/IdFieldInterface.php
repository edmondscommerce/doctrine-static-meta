<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey;

interface IdFieldInterface
{
    public const PROPERTY_NAME= 'id';

    public function getId();
}
