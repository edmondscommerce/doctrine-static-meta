<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\IpAddressFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait IpAddressFieldTrait
{

    /**
     * @var string|null
     */
    private $ipAddress;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     *
     * @see https://stackoverflow.com/a/1076755/543455
     */
    public static function metaForIpAddress(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => IpAddressFieldInterface::PROP_IP_ADDRESS,
                'type'      => Type::STRING,
                'default'   => IpAddressFieldInterface::DEFAULT_IP_ADDRESS,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(IpAddressFieldInterface::PROP_IP_ADDRESS))
            ->nullable(true)
            ->unique(false)
            ->length(45)
            ->build();
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForIpAddress(ValidatorClassMetaData $metadata)
    {
        $metadata->addPropertyConstraint(
            IpAddressFieldInterface::PROP_IP_ADDRESS,
            new Ip(IpAddressFieldInterface::IP_ADDRESS_VALIDATION_OPTIONS)
        );
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        if (null === $this->ipAddress) {
            return IpAddressFieldInterface::DEFAULT_IP_ADDRESS;
        }

        return $this->ipAddress;
    }

    /**
     * @param string|null $ipAddress
     *
     * @return self
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
