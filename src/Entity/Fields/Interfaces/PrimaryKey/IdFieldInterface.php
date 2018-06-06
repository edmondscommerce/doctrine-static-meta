<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey;

interface IdFieldInterface
{
    public const PROP_ID = 'id';

    public const DEFAULT_ID = null;

    public function getId();
}
