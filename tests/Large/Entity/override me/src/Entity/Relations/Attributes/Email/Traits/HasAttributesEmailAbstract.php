<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Email\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Attributes\Email as AttributesEmail;
use My\Test\Project\Entity\Interfaces\Attributes\EmailInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\HasAttributesEmailInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\ReciprocatesAttributesEmailInterface;

/**
 * Trait HasAttributesEmailAbstract
 *
 * The base trait for relations to a single AttributesEmail
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesEmail\Traits
 */
// phpcs:enable
trait HasAttributesEmailAbstract
{
    /**
     * @var AttributesEmail|null
     */
    private $attributesEmail;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForAttributesEmail(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForAttributesEmail(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasAttributesEmailInterface::PROPERTY_NAME_ATTRIBUTES_EMAIL,
            new Valid()
        );
    }

    /**
     * @param null|EmailInterface $attributesEmail
     * @param bool                         $recip
     *
     * @return HasAttributesEmailInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAttributesEmail(
        ?EmailInterface $attributesEmail = null,
        bool $recip = true
    ): HasAttributesEmailInterface {
        if (
            $this instanceof ReciprocatesAttributesEmailInterface
            && true === $recip
        ) {
            if (!$attributesEmail instanceof EntityInterface) {
                $attributesEmail = $this->getAttributesEmail();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $attributesEmail->$remover($this, false);
        }

        return $this->setAttributesEmail(null, false);
    }

    /**
     * @return EmailInterface|null
     */
    public function getAttributesEmail(): ?EmailInterface
    {
        return $this->attributesEmail;
    }

    /**
     * @param EmailInterface|null $attributesEmail
     * @param bool                         $recip
     *
     * @return HasAttributesEmailInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setAttributesEmail(
        ?EmailInterface $attributesEmail,
        bool $recip = true
    ): HasAttributesEmailInterface {

        $this->setEntityAndNotify('attributesEmail', $attributesEmail);
        if (
            $this instanceof ReciprocatesAttributesEmailInterface
            && true === $recip
            && null !== $attributesEmail
        ) {
            $this->reciprocateRelationOnAttributesEmail($attributesEmail);
        }

        return $this;
    }
}
