<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Binary\BinaryUuidFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Numeric\FloatWithinRangeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Numeric\IndexedUniqueIntegerFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Numeric\IntegerWithinRangeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\BusinessIdentifierCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\CountryCodeFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\DomainNameFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\EmailAddressFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\EnumFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\IpAddressFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\IsbnFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\LocaleIdentifierFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\NullableStringFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\SettableUuidFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\ShortIndexedRequiredStringFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UnicodeLanguageIdentifierFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UniqueEnumFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UniqueStringFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String\UrlFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Binary\BinaryUuidFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\FloatWithinRangeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IndexedUniqueIntegerFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Numeric\IntegerWithinRangeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\BusinessIdentifierCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\CountryCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\DomainNameFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EmailAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IsbnFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\LocaleIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\NullableStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\SettableUuidFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\ShortIndexedRequiredStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UnicodeLanguageIdentifierFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueEnumFieldInterface;
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
    // phpcs:disable
    public const FAKER_DATA_PROVIDERS = [
        BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE          => BusinessIdentifierCodeFakerData::class,
        CountryCodeFieldInterface::PROP_COUNTRY_CODE                                 => CountryCodeFakerData::class,
        EmailAddressFieldInterface::PROP_EMAIL_ADDRESS                               => EmailAddressFakerData::class,
        EnumFieldInterface::PROP_ENUM                                                => EnumFakerData::class,
        IpAddressFieldInterface::PROP_IP_ADDRESS                                     => IpAddressFakerData::class,
        IsbnFieldInterface::PROP_ISBN                                                => IsbnFakerData::class,
        LocaleIdentifierFieldInterface::PROP_LOCALE_IDENTIFIER                       => LocaleIdentifierFakerData::class,
        NullableStringFieldInterface::PROP_NULLABLE_STRING                           => NullableStringFakerData::class,
        SettableUuidFieldInterface::PROP_SETTABLE_UUID                               => SettableUuidFakerData::class,
        UnicodeLanguageIdentifierFieldInterface::PROP_UNICODE_LANGUAGE_IDENTIFIER    => UnicodeLanguageIdentifierFakerData::class,
        UniqueStringFieldInterface::PROP_UNIQUE_STRING                               => UniqueStringFakerData::class,
        UrlFieldInterface::PROP_URL                                                  => UrlFakerData::class,
        DomainNameFieldInterface::PROP_DOMAIN_NAME                                   => DomainNameFakerData::class,
        ShortIndexedRequiredStringFieldInterface::PROP_SHORT_INDEXED_REQUIRED_STRING => ShortIndexedRequiredStringFakerData::class,
        IntegerWithinRangeFieldInterface::PROP_INTEGER_WITHIN_RANGE                  => IntegerWithinRangeFakerData::class,
        FloatWithinRangeFieldInterface::PROP_FLOAT_WITHIN_RANGE                      => FloatWithinRangeFakerData::class,
        IndexedUniqueIntegerFieldInterface::PROP_INDEXED_UNIQUE_INTEGER              => IndexedUniqueIntegerFakerData::class,
        BinaryUuidFieldInterface::PROP_BINARY_UUID                                   => BinaryUuidFakerData::class,
        UniqueEnumFieldInterface::PROP_UNIQUE_ENUM                                   => UniqueEnumFakerData::class,
    ];
    // phpcs:enable
}
