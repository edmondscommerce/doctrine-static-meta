<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Email\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\Attributes\EmailInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\HasAttributesEmailsInterface;
use My\Test\Project\Entity\Relations\Attributes\Email\Interfaces\ReciprocatesAttributesEmailInterface;

/**
 * Trait HasAttributesEmailsAbstract
 *
 * The base trait for relations to multiple AttributesEmails
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesEmail\Traits
 */
// phpcs:enable
trait HasAttributesEmailsAbstract
{
    /**
     * @var ArrayCollection|EmailInterface[]
     */
    private $attributesEmails;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForAttributesEmails(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasAttributesEmailsInterface::PROPERTY_NAME_ATTRIBUTES_EMAILS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForAttributesEmails(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|EmailInterface[]
     */
    public function getAttributesEmails(): Collection
    {
        return $this->attributesEmails;
    }

    /**
     * @param Collection|EmailInterface[] $attributesEmails
     *
     * @return self
     */
    public function setAttributesEmails(
        Collection $attributesEmails
    ): HasAttributesEmailsInterface {
        $this->setEntityCollectionAndNotify(
            'attributesEmails',
            $attributesEmails
        );

        return $this;
    }

    /**
     * @param EmailInterface|null $attributesEmail
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addAttributesEmail(
        ?EmailInterface $attributesEmail,
        bool $recip = true
    ): HasAttributesEmailsInterface {
        if ($attributesEmail === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('attributesEmails', $attributesEmail);
        if ($this instanceof ReciprocatesAttributesEmailInterface && true === $recip) {
            $this->reciprocateRelationOnAttributesEmail(
                $attributesEmail
            );
        }

        return $this;
    }

    /**
     * @param EmailInterface $attributesEmail
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeAttributesEmail(
        EmailInterface $attributesEmail,
        bool $recip = true
    ): HasAttributesEmailsInterface {
        $this->removeFromEntityCollectionAndNotify('attributesEmails', $attributesEmail);
        if ($this instanceof ReciprocatesAttributesEmailInterface && true === $recip) {
            $this->removeRelationOnAttributesEmail(
                $attributesEmail
            );
        }

        return $this;
    }

    /**
     * Initialise the attributesEmails property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initAttributesEmails()
    {
        $this->attributesEmails = new ArrayCollection();

        return $this;
    }
}
