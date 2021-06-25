<?php declare(strict_types=1);
// phpcs:disable
namespace TemplateNamespace\Entity\Relations\TemplateEntity\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use TemplateNamespace\Entity\Interfaces\TemplateEntityInterface;
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
     * @var TemplateEntityInterface|null
     */
    private ?TemplateEntityInterface $templateEntity;

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
     * @throws MissingOptionsException
     * @throws InvalidOptionsException
     * @throws ConstraintDefinitionException
     */
    public static function validatorMetaForPropertyTemplateEntity(
        ValidatorClassMetaData $metadata
    ): void {
        $validConstraint           = new Valid();
        $validConstraint->traverse = false;
        $metadata->addPropertyConstraint(
            HasTemplateEntityInterface::PROPERTY_NAME_TEMPLATE_ENTITY,
            $validConstraint
        );
    }

    /**
     * @param null|TemplateEntityInterface $templateEntity
     * @param bool                         $recip
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeTemplateEntity(
        ?TemplateEntityInterface $templateEntity = null,
        bool $recip = true
    ): self {
        if (
            $this instanceof ReciprocatesTemplateEntityInterface
            && true === $recip
        ) {
            if (!$templateEntity instanceof EntityInterface) {
                $templateEntity = $this->getTemplateEntity();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            if (false !== method_exists($templateEntity, $remover)) {
                $templateEntity->$remover($this, false);
            }
        }

        return $this->setTemplateEntity(null, false);
    }

    /**
     * @return TemplateEntityInterface|null
     */
    public function getTemplateEntity(): ?TemplateEntityInterface
    {
        return $this->templateEntity;
    }

    /**
     * @param TemplateEntityInterface|null $templateEntity
     * @param bool                         $recip
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setTemplateEntity(
        ?TemplateEntityInterface $templateEntity,
        bool $recip = true
    ): self {

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
}
