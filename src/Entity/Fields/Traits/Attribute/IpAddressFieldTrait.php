<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

/**
 * Any valid IP address including version 4 and 6
 *
 * Trait IpAddressFieldTrait
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute
 */
trait IpAddressFieldTrait
{


    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @see https://stackoverflow.com/a/1076755/543455
     *
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForIpAddress(ClassMetadataBuilder $builder): void
    {
        $builder->createField(IpAddressFieldInterface::PROP_IP_ADDRESS, Type::STRING)
                ->length(45)
                ->nullable(true)
                ->build();
    }

    /**
     * @see https://symfony.com/doc/current/reference/constraints/Ip.html
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForIpAddress(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            IpAddressFieldInterface::PROP_IP_ADDRESS,
            new Ip(static::IP_ADDRESS_VALIDATION_OPTIONS)
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
    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(IpAddressFieldInterface::PROP_IP_ADDRESS);
        }

        return $this;
    }
}
