<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Testing;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\FakerData\Attribute\IpAddressFakerData;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\IpAddressFieldInterface;

interface EntityTestInterface
{
    /**
     * The function name that is called to get the instance of EntityManager
     */
    public const GET_ENTITY_MANAGER_FUNCTION_NAME = 'dsmGetEntityManagerFactory';

    /**
     * Standard library faker data provider FQNs
     *
     * This const should be overridden in your child class and extended with any project specific field data providers
     * in addition to the standard library
     *
     * The key is the column/property name and the value is the FQN for the data provider
     */
    public const FAKER_DATA_PROVIDERS = [
        IpAddressFieldInterface::PROP_IP_ADDRESS => IpAddressFakerData::class,
    ];

    /**
     * Faker can be seeded with a number which makes the generation deterministic
     * This helps to avoid tests that fail randomly
     * If you do want randomness, override this and set it to null
     */
    public const SEED = 100111991161141051101013211511697116105993210910111697.0;
}
