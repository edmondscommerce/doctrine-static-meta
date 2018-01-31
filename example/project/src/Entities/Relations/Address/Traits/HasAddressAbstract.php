<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Address\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\Entities\Relations\Address\Interfaces\ReciprocatesAddress;
use My\Test\Project\Entities\Address;

trait HasAddressAbstract
{
    /**
     * @var Address|null
     */
    private $address = null;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForAddress(ClassMetadataBuilder $builder);

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setAddress(Address $address, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesAddress && true === $recip) {
            $this->reciprocateRelationOnAddress($address);
        }
        $this->address = $address;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeAddress(): UsesPHPMetaDataInterface
    {
        $this->address = null;

        return $this;
    }
}
