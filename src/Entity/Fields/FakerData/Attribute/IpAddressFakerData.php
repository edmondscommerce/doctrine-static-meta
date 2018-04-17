<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Attribute;

use Faker\Generator;

class IpAddressFakerData
{
    private const FAKER_IP_ADDRESS_FORMATTERS = [
        'ipv4',
        'ipv6',
        'localIpv4',
    ];

    public function __invoke(Generator $generator): string
    {
        $pseudoProperty = self::FAKER_IP_ADDRESS_FORMATTERS[array_rand(self::FAKER_IP_ADDRESS_FORMATTERS)];

        return $generator->$pseudoProperty;
    }
}
