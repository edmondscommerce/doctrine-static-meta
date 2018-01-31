<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\LineItem\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order\LineItem;

trait HasLineItemsAbstract
{
    /**
     * @var ArrayCollection|LineItem[]
     */
    private $lineItems;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForLineItems(ClassMetadataBuilder $builder);

    /**
     * @return Collection|LineItem[]
     */
    public function getLineItems(): Collection
    {
        return $this->lineItems;
    }

    /**
     * @param Collection $lineItems
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
     */
    public function addLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->lineItems->contains($lineItem)) {
            $this->lineItems->add($lineItem);
            if (true === $recip) {
                $this->reciprocateRelationOnLineItem($lineItem, false);
            }
        }

        return $this;
    }

    /**
     * @param LineItem $lineItem
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->lineItems->removeElement($lineItem);
        if (true === $recip) {
            $this->removeRelationOnLineItem($lineItem, false);
        }

        return $this;
    }

    private function initLineItems()
    {
        $this->lineItems = new ArrayCollection();

        return $this;
    }
}
