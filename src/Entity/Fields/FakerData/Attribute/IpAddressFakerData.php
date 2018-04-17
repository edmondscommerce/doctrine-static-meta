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
    /**
     * @var Generator
     */
    protected $generator;

    public function __construct(Generator $generator)
    {

        $this->generator = $generator;
    }

    public function __invoke(): string
    {
        $pseudoProperty = self::FAKER_IP_ADDRESS_FORMATTERS[array_rand(self::FAKER_IP_ADDRESS_FORMATTERS)];

        return $this->generator->$pseudoProperty;
    }
}
