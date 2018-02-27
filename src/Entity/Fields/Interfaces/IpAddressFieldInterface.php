<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces;

interface IpAddressFieldInterface
{
    public const PROPERTY_NAME = 'ipAddress';

    public function getIpAddress(): string;

    public function setIpAddress(string $ipAddress): IpAddressFieldInterface;
}
