<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean;

interface DefaultsNullFieldInterface
{
    public const PROP_DEFAULTS_NULL = 'defaultsNull';

    public const DEFAULT_DEFAULTS_NULL = null;

    public function isDefaultsNull(): ?bool;

    public function setDefaultsNull(?bool $defaultsNull);
}
