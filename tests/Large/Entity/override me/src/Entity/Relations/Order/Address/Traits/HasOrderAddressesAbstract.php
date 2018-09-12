<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Address\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Order\AddressInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\HasOrderAddressesInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;

/**
 * Trait HasOrderAddressesAbstract
 *
 * The base trait for relations to multiple OrderAddresses
 *
 * @package Test\Code\Generator\Entity\Relations\OrderAddress\Traits
 */
// phpcs:enable
trait HasOrderAddressesAbstract
{
    /**
     * @var ArrayCollection|AddressInterface[]
     */
    private $orderAddresses;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForOrderAddresses(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasOrderAddressesInterface::PROPERTY_NAME_ORDER_ADDRESSES,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForOrderAddresses(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|AddressInterface[]
     */
    public function getOrderAddresses(): Collection
    {
        return $this->orderAddresses;
    }

    /**
     * @param Collection|AddressInterface[] $orderAddresses
     *
     * @return self
     */
    public function setOrderAddresses(
        Collection $orderAddresses
    ): HasOrderAddressesInterface {
        $this->setEntityCollectionAndNotify(
            'orderAddresses',
            $orderAddresses
        );

        return $this;
    }

    /**
     * @param AddressInterface|null $orderAddress
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrderAddress(
        ?AddressInterface $orderAddress,
        bool $recip = true
    ): HasOrderAddressesInterface {
        if ($orderAddress === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('orderAddresses', $orderAddress);
        if ($this instanceof ReciprocatesOrderAddressInterface && true === $recip) {
            $this->reciprocateRelationOnOrderAddress(
                $orderAddress
            );
        }

        return $this;
    }

    /**
     * @param AddressInterface $orderAddress
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrderAddress(
        AddressInterface $orderAddress,
        bool $recip = true
    ): HasOrderAddressesInterface {
        $this->removeFromEntityCollectionAndNotify('orderAddresses', $orderAddress);
        if ($this instanceof ReciprocatesOrderAddressInterface && true === $recip) {
            $this->removeRelationOnOrderAddress(
                $orderAddress
            );
        }

        return $this;
    }

    /**
     * Initialise the orderAddresses property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initOrderAddresses()
    {
        $this->orderAddresses = new ArrayCollection();

        return $this;
    }
}
