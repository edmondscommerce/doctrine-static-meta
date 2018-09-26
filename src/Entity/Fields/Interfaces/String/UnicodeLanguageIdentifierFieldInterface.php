<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface UnicodeLanguageIdentifierFieldInterface
{
    public const PROP_UNICODE_LANGUAGE_IDENTIFIER = 'unicodeLanguageIdentifier';

    public const DEFAULT_UNICODE_LANGUAGE_IDENTIFIER = null;

    public function getUnicodeLanguageIdentifier(): ?string;
}
