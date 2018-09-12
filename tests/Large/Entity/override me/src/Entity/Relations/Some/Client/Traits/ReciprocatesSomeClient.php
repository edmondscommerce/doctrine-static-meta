<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Some\Client\Traits;

// phpcs:disable
use My\Test\Project\Entities\Some\Client as SomeClient;
use My\Test\Project\Entity\Interfaces\Some\ClientInterface;
use My\Test\Project\Entity\Relations\Some\Client\Interfaces\ReciprocatesSomeClientInterface;

/**
 * Trait ReciprocatesSomeClient
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\SomeClient\Traits
 */
// phpcs:enable
trait ReciprocatesSomeClient
{
    /**
     * This method needs to set the relationship on the someClient to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param SomeClient|null $someClient
     *
     * @return ReciprocatesSomeClientInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnSomeClient(
        ClientInterface $someClient
    ): ReciprocatesSomeClientInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $someClient::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to SomeClient'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $someClient->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the someClient to this entity.
     *
     * @param SomeClient $someClient
     *
     * @return ReciprocatesSomeClientInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnSomeClient(
        ClientInterface $someClient
    ): ReciprocatesSomeClientInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $someClient->$method($this, false);

        return $this;
    }
}
