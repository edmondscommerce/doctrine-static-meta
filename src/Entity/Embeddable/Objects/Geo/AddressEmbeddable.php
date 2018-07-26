<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo;

use Doctrine\ORM\Mapping\ClassMetadata;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo\HasAddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\AbstractEmbeddableObject;
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

    /**
     * @param ClassMetadata $metadata
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
                AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_CODE,
                AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_AREA,
                AddressEmbeddableInterface::EMBEDDED_PROP_COUNTRY_CODE,
            ],
            $builder
        );
    }

    public function __toString(): string
    {
        return (string)print_r(
            [
                'addressEmbeddable' => [
                    AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NUMBER => $this->getHouseNumber(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_HOUSE_NAME   => $this->getHouseName(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_STREET       => $this->getStreet(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_CITY         => $this->getCity(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_CODE  => $this->getPostalCode(),
                    AddressEmbeddableInterface::EMBEDDED_PROP_POSTAL_AREA  => $this->getPostalArea(),
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
     * @param string $houseNumber
     *
     * @return AddressEmbeddable
     */
    public function setHouseNumber(string $houseNumber): AddressEmbeddableInterface
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
     * @return string
     */
    public function getHouseName(): string
    {
        return $this->houseName ?? '';
    }

    /**
     * @param string $houseName
     *
     * @return AddressEmbeddable
     */
    public function setHouseName(string $houseName): AddressEmbeddableInterface
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
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street ?? '';
    }

    /**
     * @param string $street
     *
     * @return AddressEmbeddable
     */
    public function setStreet(string $street): AddressEmbeddableInterface
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
     * @return string
     */
    public function getCity(): string
    {
        return $this->city ?? '';
    }

    /**
     * @param string $city
     *
     * @return AddressEmbeddable
     */
    public function setCity(string $city): AddressEmbeddableInterface
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
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode ?? '';
    }

    /**
     * @param string $postalCode
     *
     * @return AddressEmbeddable
     */
    public function setPostalCode(string $postalCode): AddressEmbeddableInterface
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
     * @return string
     */
    public function getPostalArea(): string
    {
        return $this->postalArea ?? '';
    }

    /**
     * @param string $postalArea
     *
     * @return AddressEmbeddable
     */
    public function setPostalArea(string $postalArea): AddressEmbeddableInterface
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
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode ?? '';
    }

    /**
     * @param string $countryCode
     *
     * @return AddressEmbeddable
     */
    public function setCountryCode(string $countryCode): AddressEmbeddableInterface
    {
        $this->notifyEmbeddablePrefixedProperties(
            'countryCode',
            $this->countryCode,
            $countryCode
        );
        $this->countryCode = $countryCode;

        return $this;
    }

    protected function getPrefix(): string
    {
        return HasAddressEmbeddableInterface::PROP_ADDRESS_EMBEDDABLE;
    }
}
