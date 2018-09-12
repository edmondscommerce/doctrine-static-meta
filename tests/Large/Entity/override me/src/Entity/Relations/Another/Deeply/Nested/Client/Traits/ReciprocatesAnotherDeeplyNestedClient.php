<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Traits;

// phpcs:disable
use My\Test\Project\Entities\Another\Deeply\Nested\Client as AnotherDeeplyNestedClient;
use My\Test\Project\Entity\Interfaces\Another\Deeply\Nested\ClientInterface;
use My\Test\Project\Entity\Relations\Another\Deeply\Nested\Client\Interfaces\ReciprocatesAnotherDeeplyNestedClientInterface;

/**
 * Trait ReciprocatesAnotherDeeplyNestedClient
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\AnotherDeeplyNestedClient\Traits
 */
// phpcs:enable
trait ReciprocatesAnotherDeeplyNestedClient
{
    /**
     * This method needs to set the relationship on the anotherDeeplyNestedClient to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param AnotherDeeplyNestedClient|null $anotherDeeplyNestedClient
     *
     * @return ReciprocatesAnotherDeeplyNestedClientInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnAnotherDeeplyNestedClient(
        ClientInterface $anotherDeeplyNestedClient
    ): ReciprocatesAnotherDeeplyNestedClientInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $anotherDeeplyNestedClient::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to AnotherDeeplyNestedClient'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $anotherDeeplyNestedClient->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the anotherDeeplyNestedClient to this entity.
     *
     * @param AnotherDeeplyNestedClient $anotherDeeplyNestedClient
     *
     * @return ReciprocatesAnotherDeeplyNestedClientInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnAnotherDeeplyNestedClient(
        ClientInterface $anotherDeeplyNestedClient
    ): ReciprocatesAnotherDeeplyNestedClientInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $anotherDeeplyNestedClient->$method($this, false);

        return $this;
    }
}
