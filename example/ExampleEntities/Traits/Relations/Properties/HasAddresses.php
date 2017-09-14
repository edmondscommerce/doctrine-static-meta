<?php declare(strict_types=1);


namespace Edmonds\DoctrineStaticMeta\ExampleEntities\Traits\Relations\Properties;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Edmonds\DoctrineStaticMeta\Properties\Address;

trait HasAddresses
{
    use ReciprocatesAddress;

    /**
     * @var ArrayCollection|Address[]
     */
    private $addresses;

    protected static function getPropertyMetaForAddresses(ClassMetadataBuilder $builder)
    {
        $builder->addOwningManyToMany(
            Address::getPlural(),
            Address::class,
            static::getPlural()
        );
    }

    /**
     * @return ArrayCollection|Address[]
     */
    public function getAddresses(): ArrayCollection
    {
        return $this->addresses;
    }

    /**
     * @param ArrayCollection|Address[] $addresses
     *
     * @return $this
     */
    public function setAddresses(ArrayCollection $addresses)
    {
        $this->addresses = $addresses;

        return $this;
    }

    /**
     * @param Address $address
     * @param bool    $recip
     *
     * @return $this
     */
    public function addAddress(Address $address, bool $recip = true)
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            if (true === $recip) {
                $this->reciprocateRelationOnAddress($address);
            }
        }

        return $this;
    }

    /**
     * @param Address $address
     * @param bool    $recip
     *
     * @return $this
     */
    public function removeAddress(Address $address, bool $recip = true)
    {
        $this->addresses->removeElement($address);
        if (true === $recip) {
            $this->removeRelationOnAddress($address);
        }

        return $this;
    }


}
