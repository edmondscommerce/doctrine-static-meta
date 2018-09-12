<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Address\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Interfaces\Order\AddressInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\HasOrderAddressInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;

/**
 * Trait HasOrderAddressAbstract
 *
 * The base trait for relations to a single OrderAddress
 *
 * @package Test\Code\Generator\Entity\Relations\OrderAddress\Traits
 */
// phpcs:enable
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
    abstract public static function metaForOrderAddress(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForOrderAddress(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasOrderAddressInterface::PROPERTY_NAME_ORDER_ADDRESS,
            new Valid()
        );
    }

    /**
     * @param null|AddressInterface $orderAddress
     * @param bool                         $recip
     *
     * @return HasOrderAddressInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrderAddress(
        ?AddressInterface $orderAddress = null,
        bool $recip = true
    ): HasOrderAddressInterface {
        if (
            $this instanceof ReciprocatesOrderAddressInterface
            && true === $recip
        ) {
            if (!$orderAddress instanceof EntityInterface) {
                $orderAddress = $this->getOrderAddress();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $orderAddress->$remover($this, false);
        }

        return $this->setOrderAddress(null, false);
    }

    /**
     * @return AddressInterface|null
     */
    public function getOrderAddress(): ?AddressInterface
    {
        return $this->orderAddress;
    }

    /**
     * @param AddressInterface|null $orderAddress
     * @param bool                         $recip
     *
     * @return HasOrderAddressInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrderAddress(
        ?AddressInterface $orderAddress,
        bool $recip = true
    ): HasOrderAddressInterface {

        $this->setEntityAndNotify('orderAddress', $orderAddress);
        if (
            $this instanceof ReciprocatesOrderAddressInterface
            && true === $recip
            && null !== $orderAddress
        ) {
            $this->reciprocateRelationOnOrderAddress($orderAddress);
        }

        return $this;
    }
}
