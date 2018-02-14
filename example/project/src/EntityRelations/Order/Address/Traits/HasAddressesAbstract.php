<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\Address\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address;
use My\Test\Project\EntityRelations\Order\Address\Interfaces\ReciprocatesAddress;

trait HasAddressesAbstract
{
    /**
     * @var ArrayCollection|Address[]
     */
    private $addresses;

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForAddresses(ClassMetadataBuilder $manyToManyBuilder): void;

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
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setAddresses(Collection $addresses): UsesPHPMetaDataInterface
    {
        $this->addresses = $addresses;

        return $this;
    }

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            if ($this instanceof ReciprocatesAddress && true === $recip) {
                $this->reciprocateRelationOnAddress($address);
            }
        }

        return $this;
    }

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->addresses->removeElement($address);
        if ($this instanceof ReciprocatesAddress && true === $recip) {
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
