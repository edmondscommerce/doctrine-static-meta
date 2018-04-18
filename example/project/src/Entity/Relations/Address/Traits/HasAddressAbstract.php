<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Address\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Address as Address;
use My\Test\Project\Entity\Relations\Address\Interfaces\HasAddressInterface;
use My\Test\Project\Entity\Relations\Address\Interfaces\ReciprocatesAddressInterface;

trait HasAddressAbstract
{
    /**
     * @var Address|null
     */
    private $address;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForAddress(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForAddress(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasAddressInterface::PROPERTY_NAME_ADDRESS,
            new Valid()
        );
    }

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
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAddress(
        Address $address,
        bool $recip = true
    ): self {
        if ($this instanceof ReciprocatesAddressInterface && true === $recip) {
            $this->reciprocateRelationOnAddress($address);
        }
        $this->address = $address;

        return $this;
    }

    /**
     * @return self
     */
    public function removeAddress(): self
    {
        $this->address = null;

        return $this;
    }
}
