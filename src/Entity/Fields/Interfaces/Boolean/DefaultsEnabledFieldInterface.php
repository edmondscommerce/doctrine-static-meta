<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean;

interface DefaultsEnabledFieldInterface
{
    public const PROP_DEFAULTS_ENABLED = 'defaultsEnabled';

    public const DEFAULT_DEFAULTS_ENABLED = true;

    public function isDefaultsEnabled(): bool;
}
