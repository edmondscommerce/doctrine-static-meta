<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Person\Traits;

// phpcs:disable
use My\Test\Project\Entities\Person as Person;
use My\Test\Project\Entity\Interfaces\PersonInterface;
use My\Test\Project\Entity\Relations\Person\Interfaces\ReciprocatesPersonInterface;

/**
 * Trait ReciprocatesPerson
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\Person\Traits
 */
// phpcs:enable
trait ReciprocatesPerson
{
    /**
     * This method needs to set the relationship on the person to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Person|null $person
     *
     * @return ReciprocatesPersonInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnPerson(
        PersonInterface $person
    ): ReciprocatesPersonInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $person::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to Person'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $person->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the person to this entity.
     *
     * @param Person $person
     *
     * @return ReciprocatesPersonInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnPerson(
        PersonInterface $person
    ): ReciprocatesPersonInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $person->$method($this, false);

        return $this;
    }
}
