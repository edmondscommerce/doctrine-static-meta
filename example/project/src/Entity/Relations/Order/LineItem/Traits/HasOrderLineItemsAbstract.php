<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;
use  My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\HasOrderLineItemsInterface;
use  My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\ReciprocatesOrderLineItemInterface;

trait HasOrderLineItemsAbstract
{
    /**
     * @var ArrayCollection|OrderLineItem[]
     */
    private $orderLineItems;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForOrderLineItems(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasOrderLineItemsInterface::PROPERTY_NAME_ORDER_LINE_ITEMS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForOrderLineItems(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|OrderLineItem[]
     */
    public function getOrderLineItems(): Collection
    {
        return $this->orderLineItems;
    }

    /**
     * @param Collection|OrderLineItem[] $orderLineItems
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setOrderLineItems(Collection $orderLineItems): UsesPHPMetaDataInterface
    {
        $this->orderLineItems = $orderLineItems;

        return $this;
    }

    /**
     * @param OrderLineItem $orderLineItem
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrderLineItem(
        OrderLineItem $orderLineItem,
        bool $recip = true
    ): UsesPHPMetaDataInterface {
        if (!$this->orderLineItems->contains($orderLineItem)) {
            $this->orderLineItems->add($orderLineItem);
            if ($this instanceof ReciprocatesOrderLineItemInterface && true === $recip) {
                $this->reciprocateRelationOnOrderLineItem($orderLineItem);
            }
        }

        return $this;
    }

    /**
     * @param OrderLineItem $orderLineItem
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrderLineItem(
        OrderLineItem $orderLineItem,
        bool $recip = true
    ): UsesPHPMetaDataInterface {
        $this->orderLineItems->removeElement($orderLineItem);
        if ($this instanceof ReciprocatesOrderLineItemInterface && true === $recip) {
            $this->removeRelationOnOrderLineItem($orderLineItem);
        }

        return $this;
    }

    /**
     * Initialise the orderLineItems property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initOrderLineItems()
    {
        $this->orderLineItems = new ArrayCollection();

        return $this;
    }
}
