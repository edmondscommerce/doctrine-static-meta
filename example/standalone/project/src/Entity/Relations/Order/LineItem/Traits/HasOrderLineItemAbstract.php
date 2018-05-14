<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order\LineItem as OrderLineItem;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\HasOrderLineItemInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\ReciprocatesOrderLineItemInterface;

trait HasOrderLineItemAbstract
{
    /**
     * @var OrderLineItem|null
     */
    private $orderLineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForOrderLineItem(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForOrderLineItem(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasOrderLineItemInterface::PROPERTY_NAME_ORDER_LINE_ITEM,
            new Valid()
        );
    }

    /**
     * @return OrderLineItem|null
     */
    public function getOrderLineItem(): ?OrderLineItem
    {
        return $this->orderLineItem;
    }

    /**
     * @param OrderLineItem|null $orderLineItem
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrderLineItem(
        ?OrderLineItem $orderLineItem,
        bool $recip = true
    ): HasOrderLineItemInterface {

        $this->orderLineItem = $orderLineItem;
        if ($this instanceof ReciprocatesOrderLineItemInterface
            && true === $recip
            && null !== $orderLineItem
        ) {
            $this->reciprocateRelationOnOrderLineItem($orderLineItem);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function removeOrderLineItem(): HasOrderLineItemInterface
    {
        $this->orderLineItem = null;

        return $this;
    }
}
