<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag;

interface DefaultFieldInterface
{
    public const PROP_DEFAULT = 'default';

    public function isDefault(): bool;

    public function setDefault(bool $default);
}
