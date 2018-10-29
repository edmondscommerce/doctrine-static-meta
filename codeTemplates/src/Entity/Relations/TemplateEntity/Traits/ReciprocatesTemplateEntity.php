<?php declare(strict_types=1);

namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

// phpcs:disable
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;

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
     * @param TemplateEntityInterface $templateEntity
     *
     * @return $this
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function reciprocateRelationOnTemplateEntity(
        TemplateEntityInterface $templateEntity
    ): self {
        $singular = self::getDoctrineStaticMeta()->getSingular();
        $setter   = null;
        switch (true) {
            case method_exists($templateEntity, 'add' . $singular):
                $setter = 'add' . $singular;
                break;

            case method_exists($templateEntity, 'set' . $singular):
                $setter = 'set' . $singular;
                break;
            default:
                throw new \RuntimeException(
                    'Failed to find the correct method (add|set)' . $singular
                    . ' when attempting to reciprocate the relation from '
                    . \get_class($this) . ' to TemplateEntity'
                );
        }
        $templateEntity->$setter($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the templateEntity to this entity.
     *
     * @param TemplateEntityInterface $templateEntity
     *
     * @return $this
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function removeRelationOnTemplateEntity(
        TemplateEntityInterface $templateEntity
    ): self {
        $method = 'remove' . self::getDoctrineStaticMeta()->getSingular();
        if (false === method_exists($templateEntity, $method)) {
            return $this;
        }
        $templateEntity->$method($this, false);

        return $this;
    }
}
