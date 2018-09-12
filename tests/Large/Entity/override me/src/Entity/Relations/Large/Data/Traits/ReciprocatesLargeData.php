<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Data\Traits;

// phpcs:disable
use My\Test\Project\Entities\Large\Data as LargeData;
use My\Test\Project\Entity\Interfaces\Large\DataInterface;
use My\Test\Project\Entity\Relations\Large\Data\Interfaces\ReciprocatesLargeDataInterface;

/**
 * Trait ReciprocatesLargeData
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\LargeData\Traits
 */
// phpcs:enable
trait ReciprocatesLargeData
{
    /**
     * This method needs to set the relationship on the largeData to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param LargeData|null $largeData
     *
     * @return ReciprocatesLargeDataInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnLargeData(
        DataInterface $largeData
    ): ReciprocatesLargeDataInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $largeData::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to LargeData'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $largeData->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the largeData to this entity.
     *
     * @param LargeData $largeData
     *
     * @return ReciprocatesLargeDataInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnLargeData(
        DataInterface $largeData
    ): ReciprocatesLargeDataInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $largeData->$method($this, false);

        return $this;
    }
}
