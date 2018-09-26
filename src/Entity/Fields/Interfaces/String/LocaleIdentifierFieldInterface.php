<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface LocaleIdentifierFieldInterface
{
    public const PROP_LOCALE_IDENTIFIER = 'localeIdentifier';

    public const DEFAULT_LOCALE_IDENTIFIER = null;

    public function getLocaleIdentifier(): ?string;
}
