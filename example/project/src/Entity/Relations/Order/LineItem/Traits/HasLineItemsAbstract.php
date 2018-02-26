<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\HasLineItemsInterface;
use  My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\ReciprocatesLineItemInterface;

trait HasLineItemsAbstract
{
    /**
     * @var ArrayCollection|LineItem[]
     */
    private $lineItems;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForLineItems(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasLineItemsInterface::PROPERTY_NAME_LINE_ITEMS, new Valid());
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForLineItems(ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|LineItem[]
     */
    public function getLineItems(): Collection
    {
        return $this->lineItems;
    }

    /**
     * @param Collection|LineItem[] $lineItems
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setLineItems(Collection $lineItems): UsesPHPMetaDataInterface
    {
        $this->lineItems = $lineItems;

        return $this;
    }

    /**
     * @param LineItem $lineItem
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->lineItems->contains($lineItem)) {
            $this->lineItems->add($lineItem);
            if ($this instanceof ReciprocatesLineItemInterface && true === $recip) {
                $this->reciprocateRelationOnLineItem($lineItem);
            }
        }

        return $this;
    }

    /**
     * @param LineItem $lineItem
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->lineItems->removeElement($lineItem);
        if ($this instanceof ReciprocatesLineItemInterface && true === $recip) {
            $this->removeRelationOnLineItem($lineItem);
        }

        return $this;
    }

    /**
     * Initialise the lineItems property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initLineItems()
    {
        $this->lineItems = new ArrayCollection();

        return $this;
    }
}
