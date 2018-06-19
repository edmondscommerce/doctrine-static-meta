<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\AbstractEmbeddableObjectInterface;

interface AddressEmbeddableInterface extends AbstractEmbeddableObjectInterface
{
    public const EMBEDDED_PROP_HOUSE_NUMBER = 'houseNumber';
    public const EMBEDDED_PROP_HOUSE_NAME   = 'houseName';
    public const EMBEDDED_PROP_STREET       = 'street';
    public const EMBEDDED_PROP_CITY         = 'city';
    public const EMBEDDED_PROP_POSTAL_CODE  = 'postalCode';
    public const EMBEDDED_PROP_POSTAL_AREA  = 'postalArea';
    public const EMBEDDED_PROP_COUNTRY_CODE = 'countryCode';

    /**
     * @return string
     */
    public function getHouseNumber(): string;

    /**
     * @param string $houseNumber
     *
     * @return AddressEmbeddableInterface
     */
    public function setHouseNumber(string $houseNumber): AddressEmbeddableInterface;

    /**
     * @return string
     */
    public function getHouseName(): string;

    /**
     * @param string $houseName
     *
     * @return AddressEmbeddableInterface
     */
    public function setHouseName(string $houseName): AddressEmbeddableInterface;

    /**
     * @return string
     */
    public function getStreet(): string;

    /**
     * @param string $street
     *
     * @return AddressEmbeddableInterface
     */
    public function setStreet(string $street): AddressEmbeddableInterface;

    /**
     * @return string
     */
    public function getCity(): string;

    /**
     * @param string $city
     *
     * @return AddressEmbeddableInterface
     */
    public function setCity(string $city): AddressEmbeddableInterface;

    /**
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * @param string $countryCode
     *
     * @return AddressEmbeddableInterface
     */
    public function setCountryCode(string $countryCode): AddressEmbeddableInterface;

    /**
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * @param string $postalCode
     *
     * @return AddressEmbeddableInterface
     */
    public function setPostalCode(string $postalCode): AddressEmbeddableInterface;

    /**
     * @return string
     */
    public function getPostalArea(): string;

    /**
     * @param string $postalArea
     *
     * @return AddressEmbeddableInterface
     */
    public function setPostalArea(string $postalArea): AddressEmbeddableInterface;
}
