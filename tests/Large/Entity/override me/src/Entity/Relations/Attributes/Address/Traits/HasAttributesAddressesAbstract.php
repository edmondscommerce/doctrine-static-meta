<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Attributes\AddressInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\HasAttributesAddressesInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\ReciprocatesAttributesAddressInterface;

/**
 * Trait HasAttributesAddressesAbstract
 *
 * The base trait for relations to multiple AttributesAddresses
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesAddress\Traits
 */
// phpcs:enable
trait HasAttributesAddressesAbstract
{
    /**
     * @var ArrayCollection|AddressInterface[]
     */
    private $attributesAddresses;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForAttributesAddresses(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasAttributesAddressesInterface::PROPERTY_NAME_ATTRIBUTES_ADDRESSES,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForAttributesAddresses(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|AddressInterface[]
     */
    public function getAttributesAddresses(): Collection
    {
        return $this->attributesAddresses;
    }

    /**
     * @param Collection|AddressInterface[] $attributesAddresses
     *
     * @return self
     */
    public function setAttributesAddresses(
        Collection $attributesAddresses
    ): HasAttributesAddressesInterface {
        $this->setEntityCollectionAndNotify(
            'attributesAddresses',
            $attributesAddresses
        );

        return $this;
    }

    /**
     * @param AddressInterface|null $attributesAddress
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAttributesAddress(
        ?AddressInterface $attributesAddress,
        bool $recip = true
    ): HasAttributesAddressesInterface {
        if ($attributesAddress === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('attributesAddresses', $attributesAddress);
        if ($this instanceof ReciprocatesAttributesAddressInterface && true === $recip) {
            $this->reciprocateRelationOnAttributesAddress(
                $attributesAddress
            );
        }

        return $this;
    }

    /**
     * @param AddressInterface $attributesAddress
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAttributesAddress(
        AddressInterface $attributesAddress,
        bool $recip = true
    ): HasAttributesAddressesInterface {
        $this->removeFromEntityCollectionAndNotify('attributesAddresses', $attributesAddress);
        if ($this instanceof ReciprocatesAttributesAddressInterface && true === $recip) {
            $this->removeRelationOnAttributesAddress(
                $attributesAddress
            );
        }

        return $this;
    }

    /**
     * Initialise the attributesAddresses property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initAttributesAddresses()
    {
        $this->attributesAddresses = new ArrayCollection();

        return $this;
    }
}
