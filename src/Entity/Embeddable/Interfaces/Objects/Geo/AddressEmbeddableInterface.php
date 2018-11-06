<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\AbstractEmbeddableObjectInterface;

interface AddressEmbeddableInterface extends AbstractEmbeddableObjectInterface
{
    public const EMBEDDED_PROP_HOUSE_NUMBER = 'houseNumber';
    public const EMBEDDED_PROP_HOUSE_NAME   = 'houseName';
    public const EMBEDDED_PROP_STREET       = 'street';
    public const EMBEDDED_PROP_CITY         = 'city';
    public const EMBEDDED_PROP_POSTAL_AREA  = 'postalArea';
    public const EMBEDDED_PROP_POSTAL_CODE  = 'postalCode';
    public const EMBEDDED_PROP_COUNTRY_CODE = 'countryCode';
    public const DEFAULTS                   = [
        '',
        '',
        '',
        '',
        '',
        '',
        '',
    ];

    /**
     * @return string
     */
    public function getHouseNumber(): string;

    /**
     * @return string
     */
    public function getHouseName(): string;

    /**
     * @return string
     */
    public function getStreet(): string;

    /**
     * @return string
     */
    public function getCity(): string;

    /**
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * @return string
     */
    public function getPostalArea(): string;

}
