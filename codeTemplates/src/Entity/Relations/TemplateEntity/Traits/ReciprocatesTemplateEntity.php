<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

// phpcs:disable
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\ReciprocatesTemplateEntityInterface;

/**
 * Trait ReciprocatesTemplateEntity
 *
 * This trait provides functionality for reciprocating relations as required
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits
 */
// phpcs:enable
trait ReciprocatesTemplateEntity
{
    /**
     * This method needs to set the relationship on the templateEntity to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param TemplateEntity|null $templateEntity
     *
     * @return ReciprocatesTemplateEntityInterface
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnTemplateEntity(
        TemplateEntityInterface $templateEntity
    ): ReciprocatesTemplateEntityInterface {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setters  = $templateEntity::getDoctrineStaticMeta()->getSetters();
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
                . \get_class($this) . ' to TemplateEntity'
                . "\n" . ' setters checked are: ' . var_export($setters, true)
                . "\n" . ' singular is: ' . $singular
            );
        }

        $templateEntity->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the templateEntity to this entity.
     *
     * @param TemplateEntity $templateEntity
     *
     * @return ReciprocatesTemplateEntityInterface
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnTemplateEntity(
        TemplateEntityInterface $templateEntity
    ): ReciprocatesTemplateEntityInterface {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        $templateEntity->$method($this, false);

        return $this;
    }
}
