<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Interfaces\Attributes\AddressInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\HasAttributesAddressInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\ReciprocatesAttributesAddressInterface;

/**
 * Trait HasAttributesAddressAbstract
 *
 * The base trait for relations to a single AttributesAddress
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesAddress\Traits
 */
// phpcs:enable
trait HasAttributesAddressAbstract
{
    /**
     * @var AttributesAddress|null
     */
    private $attributesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForAttributesAddress(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForAttributesAddress(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasAttributesAddressInterface::PROPERTY_NAME_ATTRIBUTES_ADDRESS,
            new Valid()
        );
    }

    /**
     * @param null|AddressInterface $attributesAddress
     * @param bool                         $recip
     *
     * @return HasAttributesAddressInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAttributesAddress(
        ?AddressInterface $attributesAddress = null,
        bool $recip = true
    ): HasAttributesAddressInterface {
        if (
            $this instanceof ReciprocatesAttributesAddressInterface
            && true === $recip
        ) {
            if (!$attributesAddress instanceof EntityInterface) {
                $attributesAddress = $this->getAttributesAddress();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $attributesAddress->$remover($this, false);
        }

        return $this->setAttributesAddress(null, false);
    }

    /**
     * @return AddressInterface|null
     */
    public function getAttributesAddress(): ?AddressInterface
    {
        return $this->attributesAddress;
    }

    /**
     * @param AddressInterface|null $attributesAddress
     * @param bool                         $recip
     *
     * @return HasAttributesAddressInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAttributesAddress(
        ?AddressInterface $attributesAddress,
        bool $recip = true
    ): HasAttributesAddressInterface {

        $this->setEntityAndNotify('attributesAddress', $attributesAddress);
        if (
            $this instanceof ReciprocatesAttributesAddressInterface
            && true === $recip
            && null !== $attributesAddress
        ) {
            $this->reciprocateRelationOnAttributesAddress($attributesAddress);
        }

        return $this;
    }
}
