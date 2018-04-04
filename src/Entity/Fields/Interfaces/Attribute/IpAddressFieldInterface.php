<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute;

interface IpAddressFieldInterface
{
    public const PROP_NAME = 'ipAddress';

    public function getIpAddress(): ?string;

    public function setIpAddress(?string $ipAddress): IpAddressFieldInterface;
}
