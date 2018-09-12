<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Property\Traits;

// phpcs:disable
use My\Test\Project\Entities\Large\Property as LargeProperty;
use My\Test\Project\Entity\Interfaces\Large\PropertyInterface;
use My\Test\Project\Entity\Relations\Large\Property\Interfaces\ReciprocatesLargePropertyInterface;

/**
 * Trait ReciprocatesLargeProperty
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\LargeProperty\Traits
 */
// phpcs:enable
trait ReciprocatesLargeProperty
{
    /**
     * This method needs to set the relationship on the largeProperty to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param LargeProperty|null $largeProperty
     *
     * @return ReciprocatesLargePropertyInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnLargeProperty(
        PropertyInterface $largeProperty
    ): ReciprocatesLargePropertyInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $largeProperty::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to LargeProperty'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $largeProperty->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the largeProperty to this entity.
     *
     * @param LargeProperty $largeProperty
     *
     * @return ReciprocatesLargePropertyInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnLargeProperty(
        PropertyInterface $largeProperty
    ): ReciprocatesLargePropertyInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $largeProperty->$method($this, false);

        return $this;
    }
}
