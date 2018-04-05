<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use  My\Test\Project\Entity\Relations\Order\Address\Interfaces\HasOrderAddressesInterface;
use  My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;

trait HasOrderAddressesAbstract
{
    /**
     * @var ArrayCollection|OrderAddress[]
     */
    private $orderAddresses;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForOrderAddresses(ValidatorClassMetaData $metadata): void
    {
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
    abstract public static function getPropertyDoctrineMetaForOrderAddresses(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|OrderAddress[]
     */
    public function getOrderAddresses(): Collection
    {
        return $this->orderAddresses;
    }

    /**
     * @param Collection|OrderAddress[] $orderAddresses
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setOrderAddresses(Collection $orderAddresses): UsesPHPMetaDataInterface
    {
        $this->orderAddresses = $orderAddresses;

        return $this;
    }

    /**
     * @param OrderAddress $orderAddress
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrderAddress(
        OrderAddress $orderAddress,
        bool $recip = true
    ): UsesPHPMetaDataInterface {
        if (!$this->orderAddresses->contains($orderAddress)) {
            $this->orderAddresses->add($orderAddress);
            if ($this instanceof ReciprocatesOrderAddressInterface && true === $recip) {
                $this->reciprocateRelationOnOrderAddress($orderAddress);
            }
        }

        return $this;
    }

    /**
     * @param OrderAddress $orderAddress
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrderAddress(
        OrderAddress $orderAddress,
        bool $recip = true
    ): UsesPHPMetaDataInterface {
        $this->orderAddresses->removeElement($orderAddress);
        if ($this instanceof ReciprocatesOrderAddressInterface && true === $recip) {
            $this->removeRelationOnOrderAddress($orderAddress);
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
