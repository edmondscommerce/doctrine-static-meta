<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\String;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\AbstractFakerDataProvider;

class IpAddressFakerData extends AbstractFakerDataProvider
{
    private const FORMATTERS = [
        'ipv4',
        'ipv6',
        'localIpv4',
    ];

    public function __invoke(): string
    {
        $pseudoProperty = self::FORMATTERS[array_rand(self::FORMATTERS)];

        return $this->generator->$pseudoProperty;
    }
}
