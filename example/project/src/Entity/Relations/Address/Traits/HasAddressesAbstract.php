<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Address as Address;
use  My\Test\Project\Entity\Relations\Address\Interfaces\HasAddressesInterface;
use  My\Test\Project\Entity\Relations\Address\Interfaces\ReciprocatesAddressInterface;

trait HasAddressesAbstract
{
    /**
     * @var ArrayCollection|Address[]
     */
    private $addresses;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForAddresses(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasAddressesInterface::PROPERTY_NAME_ADDRESSES,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForAddresses(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    /**
     * @param Collection|Address[] $addresses
     *
     * @return self
     */
    public function setAddresses(Collection $addresses): self
    {
        $this->addresses = $addresses;

        return $this;
    }

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAddress(
        Address $address,
        bool $recip = true
    ): self {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            if ($this instanceof ReciprocatesAddressInterface && true === $recip) {
                $this->reciprocateRelationOnAddress($address);
            }
        }

        return $this;
    }

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAddress(
        Address $address,
        bool $recip = true
    ): self {
        $this->addresses->removeElement($address);
        if ($this instanceof ReciprocatesAddressInterface && true === $recip) {
            $this->removeRelationOnAddress($address);
        }

        return $this;
    }

    /**
     * Initialise the addresses property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initAddresses()
    {
        $this->addresses = new ArrayCollection();

        return $this;
    }
}
