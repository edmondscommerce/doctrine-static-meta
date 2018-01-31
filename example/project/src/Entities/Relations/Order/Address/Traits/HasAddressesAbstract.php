<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Address\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\Address;

trait HasAddressesAbstract
{
    /**
     * @var ArrayCollection|Address[]
     */
    private $addresses;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder);

    /**
     * @return Collection|Address[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    /**
     * @param Collection $addresses
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
     */
    public function addAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            if (true === $recip) {
                $this->reciprocateRelationOnAddress($address, false);
            }
        }

        return $this;
    }

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->addresses->removeElement($address);
        if (true === $recip) {
            $this->removeRelationOnAddress($address, false);
        }

        return $this;
    }

    private function initAddresses()
    {
        $this->addresses = new ArrayCollection();

        return $this;
    }
}
