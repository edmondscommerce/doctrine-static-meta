<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\HasOrderAddressInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;

trait HasOrderAddressAbstract
{
    /**
     * @var OrderAddress|null
     */
    private $orderAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForOrderAddress(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForOrderAddress(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasOrderAddressInterface::PROPERTY_NAME_ORDER_ADDRESS,
            new Valid()
        );
    }

    /**
     * @return OrderAddress|null
     */
    public function getOrderAddress(): ?OrderAddress
    {
        return $this->orderAddress;
    }

    /**
     * @param OrderAddress $orderAddress
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrderAddress(
        OrderAddress $orderAddress,
        bool $recip = true
    ): self {
        if ($this instanceof ReciprocatesOrderAddressInterface && true === $recip) {
            $this->reciprocateRelationOnOrderAddress($orderAddress);
        }
        $this->orderAddress = $orderAddress;

        return $this;
    }

    /**
     * @return self
     */
    public function removeOrderAddress(): self
    {
        $this->orderAddress = null;

        return $this;
    }
}
