<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Traits\Fields;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

trait IpAddressField
{
    /**
     * @var string
     */
    private $ipAddress;

    protected static function getPropertyMetaForIpAddress(ClassMetadataBuilder $builder)
    {
        $builder->createField('ipAddress', Type::STRING)
            ->length(20)
            ->nullable(true)
            ->build();
    }

    /**
     * Get ipAddress
     *
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     *
     * @return $this
     */
    public function setIpAddress(string $ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }
}
