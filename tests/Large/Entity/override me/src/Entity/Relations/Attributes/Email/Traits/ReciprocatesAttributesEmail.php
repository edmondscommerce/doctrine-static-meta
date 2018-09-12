<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Attributes\Email\Traits;

// phpcs:disable
use My\Test\Project\Entities\Attributes\Email as AttributesEmail;
use My\Test\Project\Entity\Interfaces\Attributes\EmailInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\ReciprocatesAttributesEmailInterface;

/**
 * Trait ReciprocatesAttributesEmail
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesEmail\Traits
 */
// phpcs:enable
trait ReciprocatesAttributesEmail
{
    /**
     * This method needs to set the relationship on the attributesEmail to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param AttributesEmail|null $attributesEmail
     *
     * @return ReciprocatesAttributesEmailInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnAttributesEmail(
        EmailInterface $attributesEmail
    ): ReciprocatesAttributesEmailInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $attributesEmail::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to AttributesEmail'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $attributesEmail->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the attributesEmail to this entity.
     *
     * @param AttributesEmail $attributesEmail
     *
     * @return ReciprocatesAttributesEmailInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnAttributesEmail(
        EmailInterface $attributesEmail
    ): ReciprocatesAttributesEmailInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $attributesEmail->$method($this, false);

        return $this;
    }
}
