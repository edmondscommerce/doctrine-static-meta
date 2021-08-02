<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo\HasAddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

class AddressEmbeddable extends AbstractEmbeddableObject implements AddressEmbeddableInterface
{
    /**
     * @var string
     */
    private $houseNumber;
    /**
     * @var string
     */
    private $houseName;
    /**
     * @var string
     */
    private $street;
    /**
     * @var string
     */
    private $city;
    /**
     * @var string
     */
    private $countryCode;
    /**
     * @var string
     */
    private $postalCode;
    /**
     * @var string
     */
    private $postalArea;

    final public function __construct(
        string $houseNumber,
        string $houseName,
        string $street,
        string $city,
        string $postalArea,
        string $postalCode,
        string $countryCode
    ) {
        $this->setHouseNumber($houseNumber);
        $this->setHouseName($houseName);
        $this->setStreet($street);
        $this->setCity($city);
        $this->setPostalArea($postalArea);
        $this->setPostalCode($postalCode);
        $this->setCountryCode($countryCode);
    }

    /**
     * @param string $houseNumber
     *
     * @return AddressEmbeddable
     */
    private function setHouseNumber(string $houseNumber): AddressEmbeddableInterface
    {
        $this->notifyEmbeddablePrefixedProperties(
            'houseNumber',
            $this->houseNumber,
            $houseNumber
        );
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * @param string $houseName
     *
     * @return AddressEmbeddable
     */
    private function setHouseName(string $houseName): AddressEmbeddableInterface
    {
        $this->notifyEmbeddablePrefixedProperties(
            'houseName',
            $this->houseName,
            $houseName
        );
        $this->houseName = $houseName;

        return $this;
    }

    /**
     * @param string $street
     *
     * @return AddressEmbeddable
     */
    private function setStreet(string $street): AddressEmbeddableInterface
    {
        $this->notifyEmbeddablePrefixedProperties(
            'street',
            $this->street,
            $street
        );
        $this->street = $street;

        return $this;
    }

    /**
     * @param string $city
     *
     * @return AddressEmbeddable
     */
    private function setCity(string $city): AddressEmbeddableInterface
    {
        $this->notifyEmbeddablePrefixedProperties(
            'city',
            $this->city,
            $city
        );
        $this->city = $city;

        return $this;
    }

    /**
     * @param string $countryCode
     *
     * @return AddressEmbeddable
     */
    private function setCountryCode(string $countryCode): AddressEmbeddableInterface
    {
        $this->notifyEmbeddablePrefixedProperties(
            'countryCode',
            $this->countryCode,
            $countryCode
        );
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @param string $postalCode
     *
     * @return AddressEmbeddable
     */
    private function setPostalCode(string $postalCode): AddressEmbeddableInterface
    {
        $this->notifyEmbeddablePrefixedProperties(
            'postalCode',
            $this->postalCode,
            $postalCode
        );
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @param string $postalArea
     *
     * @return AddressEmbeddable
     */
    private function setPostalArea(string $postalArea): AddressEmbeddableInterface
    {
        $this->notifyEmbeddablePrefixedProperties(
            'postalArea',
            $this->postalArea,
            $postalArea
        );
        $this->postalArea = $postalArea;

        return $this;
    }

    /**
     * @param ClassMetadata<EntityInterface> $metadata
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $builder = self::setEmbeddableAndGetBuilder($metadata);
        MappingHelper::setSimpleStringFields(
            [
                AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NUMBER,
                AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NAME,
                AddressEmbeddableInterface::EMBEDDED_PROP_STREET,
                AddressEmbeddableInterface::EMBEDDED_PROP_CITY,
                AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_AREA,
                AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_CODE,
                AddressEmbeddableInterface::EMBEDDED_PROP_COUNTRY_CODE,
            ],
            $builder
        );
    }

    /**
     * @param array $properties
     */
    public static function create(array $properties): static
    {
        if (array_key_exists(AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NUMBER, $properties)) {
            return new static(
                $properties[AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NUMBER],
                $properties[AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NAME],
                $properties[AddressEmbeddableInterface::EMBEDDED_PROP_STREET],
                $properties[AddressEmbeddableInterface::EMBEDDED_PROP_CITY],
                $properties[AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_AREA],
                $properties[AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_CODE],
                $properties[AddressEmbeddableInterface::EMBEDDED_PROP_COUNTRY_CODE]
            );
        }

        return new static(...array_values($properties));
    }

    public function __toString(): string
    {
        return print_r(
            [
                'addressEmbeddable' => [
                    AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NUMBER => $this->getHouseNumber(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NAME   => $this->getHouseName(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_STREET       => $this->getStreet(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_CITY         => $this->getCity(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_AREA  => $this->getPostalArea(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_CODE  => $this->getPostalCode(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_COUNTRY_CODE => $this->getCountryCode(),
                ],
            ],
            true
        );
    }

    /**
     * @return string
     */
    public function getHouseNumber(): string
    {
        return $this->houseNumber ?? '';
    }

    /**
     * @return string
     */
    public function getHouseName(): string
    {
        return $this->houseName ?? '';
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street ?? '';
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city ?? '';
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode ?? '';
    }

    /**
     * @return string
     */
    public function getPostalArea(): string
    {
        return $this->postalArea ?? '';
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode ?? '';
    }

    protected function getPrefix(): string
    {
        return HasAddressEmbeddableInterface::PROP_ADDRESS_EMBEDDABLE;
    }
}
