<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute;

interface IpAddressFieldInterface
{
    public const PROP_IP_ADDRESS = 'ipAddress';

    /**
     * @see https://symfony.com/doc/current/reference/constraints/Ip.html
     *
     * Override this const in your entity class as required
     */
    public const IP_ADDRESS_VALIDATION_OPTIONS = [
        'version' => 'all',
    ];

    public function getIpAddress(): ?string;

    public function setIpAddress(?string $ipAddress);
}
