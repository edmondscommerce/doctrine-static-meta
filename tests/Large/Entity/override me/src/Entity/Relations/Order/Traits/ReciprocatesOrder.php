<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Traits;

// phpcs:disable
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Interfaces\OrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;

/**
 * Trait ReciprocatesOrder
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\Order\Traits
 */
// phpcs:enable
trait ReciprocatesOrder
{
    /**
     * This method needs to set the relationship on the order to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Order|null $order
     *
     * @return ReciprocatesOrderInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnOrder(
        OrderInterface $order
    ): ReciprocatesOrderInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $order::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to Order'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $order->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the order to this entity.
     *
     * @param Order $order
     *
     * @return ReciprocatesOrderInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnOrder(
        OrderInterface $order
    ): ReciprocatesOrderInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $order->$method($this, false);

        return $this;
    }
}
