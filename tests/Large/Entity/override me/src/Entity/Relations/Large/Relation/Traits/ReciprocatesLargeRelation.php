<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Large\Relation\Traits;

// phpcs:disable
use My\Test\Project\Entities\Large\Relation as LargeRelation;
use My\Test\Project\Entity\Interfaces\Large\RelationInterface;
use My\Test\Project\Entity\Relations\Large\Relation\Interfaces\ReciprocatesLargeRelationInterface;

/**
 * Trait ReciprocatesLargeRelation
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\LargeRelation\Traits
 */
// phpcs:enable
trait ReciprocatesLargeRelation
{
    /**
     * This method needs to set the relationship on the largeRelation to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param LargeRelation|null $largeRelation
     *
     * @return ReciprocatesLargeRelationInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnLargeRelation(
        RelationInterface $largeRelation
    ): ReciprocatesLargeRelationInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $largeRelation::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to LargeRelation'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $largeRelation->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the largeRelation to this entity.
     *
     * @param LargeRelation $largeRelation
     *
     * @return ReciprocatesLargeRelationInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnLargeRelation(
        RelationInterface $largeRelation
    ): ReciprocatesLargeRelationInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $largeRelation->$method($this, false);

        return $this;
    }
}
