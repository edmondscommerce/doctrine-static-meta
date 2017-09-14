<?php declare(strict_types=1);


namespace Edmonds\DoctrineStaticMeta\ExampleEntities\Traits\Relations\Properties;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Edmonds\DoctrineStaticMeta\Properties\PhoneNumber;

trait HasPhoneNumbers
{
    use ReciprocatesPhoneNumber;

    /**
     * @var ArrayCollection|PhoneNumber[]
     */
    private $phoneNumbers;

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function getPropertyMetaForPhoneNumbers(ClassMetadataBuilder $builder)
    {
        $builder->addOwningManyToMany(
            PhoneNumber::getPlural(),
            PhoneNumber::class,
            static::getPlural()
        );
    }

    /**
     * @return ArrayCollection|PhoneNumber[]
     */
    public function getPhoneNumbers(): ArrayCollection
    {
        return $this->phoneNumbers;
    }

    /**
     * @param ArrayCollection|PhoneNumber[] $phoneNumbers
     * @return $this
     */
    public function setPhoneNumbers(ArrayCollection $phoneNumbers)
    {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }

    /**
     * @param PhoneNumber $phoneNumber
     * @param bool        $recip
     *
     * @return $this
     */
    public function addPhoneNumber(PhoneNumber $phoneNumber, bool $recip = true)
    {
        if (!$this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers->add($phoneNumber);
            if (true === $recip) {
                $this->reciprocateRelationOnPhoneNumber($phoneNumber);
            }
        }

        return $this;
    }

    /**
     * @param PhoneNumber $phoneNumber
     * @param bool        $recip
     *
     * @return $this
     */
    public function removePhoneNumber(PhoneNumber $phoneNumber, bool $recip = true)
    {

        $this->phoneNumbers->removeElement($phoneNumber);
        if (true === $recip) {
            $this->removeRelationOnPhoneNumber($phoneNumber);
        }

        return $this;
    }


}
