<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\LineItem\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;
use  My\Test\Project\EntityRelations\Order\LineItem\Interfaces\ReciprocatesLineItem;

trait HasLineItemsAbstract
{
    /**
     * @var ArrayCollection|LineItem[]
     */
    private $lineItems;

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForLineItems(ClassMetadataBuilder $manyToManyBuilder): void;

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
            if ($this instanceof ReciprocatesLineItem && true === $recip) {
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
        if ($this instanceof ReciprocatesLineItem && true === $recip) {
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
