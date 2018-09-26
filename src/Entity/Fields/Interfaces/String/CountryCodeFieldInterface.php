<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String;

interface CountryCodeFieldInterface
{
    public const PROP_COUNTRY_CODE = 'countryCode';

    public const DEFAULT_COUNTRY_CODE = null;

    public function getCountryCode(): ?string;
}
