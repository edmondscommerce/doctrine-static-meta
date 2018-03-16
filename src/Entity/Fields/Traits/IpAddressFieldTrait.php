<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\IpAddressFieldInterface;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait IpAddressFieldTrait
{

    /**
     * @var string
     */
    private $ipAddress;

    protected static function getPropertyDoctrineMetaForIpAddress(ClassMetadataBuilder $builder): void
    {
        $builder->createField(IpAddressFieldInterface::PROP_NAME, Type::STRING)
                ->length(20)
                ->nullable(true)
                ->build();
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForIpAddress(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            IpAddressFieldInterface::PROP_NAME,
            new Ip()
        );
    }

    /**
     * Get ipAddress
     *
     * @return string
     */
    public function getIpAddress(): ?string
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
    public function setIpAddress(?string $ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }
}
