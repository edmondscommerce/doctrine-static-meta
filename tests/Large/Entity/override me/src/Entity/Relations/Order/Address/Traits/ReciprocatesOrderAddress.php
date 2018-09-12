<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Address\Traits;

// phpcs:disable
use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Interfaces\Order\AddressInterface;
use My\Test\Project\Entity\Relations\Order\Address\Interfaces\ReciprocatesOrderAddressInterface;

/**
 * Trait ReciprocatesOrderAddress
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\OrderAddress\Traits
 */
// phpcs:enable
trait ReciprocatesOrderAddress
{
    /**
     * This method needs to set the relationship on the orderAddress to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param OrderAddress|null $orderAddress
     *
     * @return ReciprocatesOrderAddressInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnOrderAddress(
        AddressInterface $orderAddress
    ): ReciprocatesOrderAddressInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $orderAddress::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to OrderAddress'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $orderAddress->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the orderAddress to this entity.
     *
     * @param OrderAddress $orderAddress
     *
     * @return ReciprocatesOrderAddressInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnOrderAddress(
        AddressInterface $orderAddress
    ): ReciprocatesOrderAddressInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $orderAddress->$method($this, false);

        return $this;
    }
}
