<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entities\TemplateEntity as TemplateEntity;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\HasTemplateEntityInterface;
use TemplateNamespace\Entity\Relations\TemplateEntity\Interfaces\ReciprocatesTemplateEntityInterface;

/**
 * Trait HasTemplateEntityAbstract
 *
 * The base trait for relations to a single TemplateEntity
 *
 * @package TemplateNamespace\Entity\Relations\TemplateEntity\Traits
 */
// phpcs:enable
trait HasTemplateEntityAbstract
{
    /**
     * @var TemplateEntity|null
     */
    private $templateEntity;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForTemplateEntity(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForTemplateEntity(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasTemplateEntityInterface::PROPERTY_NAME_TEMPLATE_ENTITY,
            new Valid()
        );
    }

    /**
     * @return TemplateEntity|null
     */
    public function getTemplateEntity(): ?TemplateEntity
    {
        return $this->templateEntity;
    }

    /**
     * @param TemplateEntity|null $templateEntity
     * @param bool                $recip
     *
     * @return HasTemplateEntityInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(
        ?TemplateEntity $templateEntity,
        bool $recip = true
    ): HasTemplateEntityInterface {

        $this->setEntityAndNotify('templateEntity', $templateEntity);
        if (
            $this instanceof ReciprocatesTemplateEntityInterface
            && true === $recip
            && null !== $templateEntity
        ) {
            $this->reciprocateRelationOnTemplateEntity($templateEntity);
        }

        return $this;
    }

    /**
     * @param null|TemplateEntity $templateEntity
     * @param bool                $recip
     *
     * @return HasTemplateEntityInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeTemplateEntity(
        ?TemplateEntity $templateEntity = null,
        bool $recip = true
    ): HasTemplateEntityInterface {
        if (
            $this instanceof ReciprocatesTemplateEntityInterface
            && true === $recip
        ) {
            if (!$templateEntity instanceof EntityInterface) {
                $templateEntity = $this->getTemplateEntity();
            }
            $remover = 'remove'.static::$doctrineStaticMeta->getSingular();
            $templateEntity->$remover($this, false);
        }

        return $this->setTemplateEntity(null, false);
    }
}
