<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits;

// phpcs:disable
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Interfaces\Attributes\AddressInterface;
use My\Test\Project\Entity\Relations\Attributes\Address\Interfaces\ReciprocatesAttributesAddressInterface;

/**
 * Trait ReciprocatesAttributesAddress
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesAddress\Traits
 */
// phpcs:enable
trait ReciprocatesAttributesAddress
{
    /**
     * This method needs to set the relationship on the attributesAddress to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param AttributesAddress|null $attributesAddress
     *
     * @return ReciprocatesAttributesAddressInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnAttributesAddress(
        AddressInterface $attributesAddress
    ): ReciprocatesAttributesAddressInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $attributesAddress::getDoctrineStaticMeta()->getSetters();
        $setter   = null;
        foreach ($setters as $method) {
            if (0 === \strcasecmp($method, 'add' . $singular)) {
                $setter = $method;
                break;
            }
            if (0 === \strcasecmp($method, 'set' . $singular)) {
                $setter = $method;
                break;
            }
        }
        if (null === $setter) {
            throw new \RuntimeException(
                'Failed to find the correct method '
                . 'when attempting to reciprocate the relation from '
                . \get_class($this) . ' to AttributesAddress'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $attributesAddress->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the attributesAddress to this entity.
     *
     * @param AttributesAddress $attributesAddress
     *
     * @return ReciprocatesAttributesAddressInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnAttributesAddress(
        AddressInterface $attributesAddress
    ): ReciprocatesAttributesAddressInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $attributesAddress->$method($this, false);

        return $this;
    }
}
