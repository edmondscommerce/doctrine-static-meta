<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute;

interface IpAddressFieldInterface
{
    public const PROP_IP_ADDRESS = 'ipAddress';

    public function getIpAddress(): ?string;

    public function setIpAddress(?string $ipAddress);
}
