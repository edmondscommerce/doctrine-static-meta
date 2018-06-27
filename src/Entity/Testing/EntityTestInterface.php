<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\BusinessIdentifierCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\CountryCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\EmailAddressFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\EnumFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\IpAddressFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\IsbnFakerDataProvider;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\LocaleIdentifierFakerDataProvider;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\NullableStringFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\SettableUuidFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UnicodeLanguageIdentifierFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UniqueStringFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UrlFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\BusinessIdentifierCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\CountryCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\LocaleIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\NullableStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\SettableUuidFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UnicodeLanguageIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UrlFieldInterface;

interface EntityTestInterface
{
    /**
     * The function name that is called to get the instance of EntityManager
     */
    public const GET_ENTITY_MANAGER_FUNCTION_NAME = 'dsmGetEntityManagerFactory';

    /**
     * Faker can be seeded with a number which makes the generation deterministic
     * This helps to avoid tests that fail randomly
     * If you do want randomness, override this and set it to null
     */
    public const SEED = 100111991161141051101013211511697116105993210910111697.0;

    /**
     * Standard library faker data provider FQNs
     *
     * This const should be overridden in your child class and extended with any project specific field data providers
     * in addition to the standard library
     *
     * The key is the column/property name and the value is the FQN for the data provider
     */
    public const FAKER_DATA_PROVIDERS = [
        BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE => BusinessIdentifierCodeFakerData::class,
        CountryCodeFieldInterface::PROP_COUNTRY_CODE                        => CountryCodeFakerData::class,
        EmailAddressFieldInterface::PROP_EMAIL_ADDRESS                      => EmailAddressFakerData::class,
        EnumFieldInterface::PROP_ENUM                                       => EnumFakerData::class,
        IpAddressFieldInterface::PROP_IP_ADDRESS                            => IpAddressFakerData::class,
        IsbnFieldInterface::PROP_ISBN                                       => IsbnFakerDataProvider::class,
        LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER              => LocaleIdentifierFakerDataProvider::class,
        NullableStringFieldInterface::PROP_NULLABLE_STRING                  => NullableStringFakerData::class,
        SettableUuidFieldInterface::PROP_SETTABLE_UUID                      => SettableUuidFakerData::class,
        UnicodeLanguageIdentifierFieldInterface::PROP_UNICODE_LANGUAGE_IDENTIFIER
                                                                            =>
            UnicodeLanguageIdentifierFakerData::class,
        UniqueStringFieldInterface::PROP_UNIQUE_STRING                      => UniqueStringFakerData::class,
        UrlFieldInterface::PROP_URL                                         => UrlFakerData::class,
    ];
}
