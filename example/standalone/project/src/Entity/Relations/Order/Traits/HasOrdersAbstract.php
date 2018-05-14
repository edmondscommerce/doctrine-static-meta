<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrdersInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;

trait HasOrdersAbstract
{
    /**
     * @var ArrayCollection|Order[]
     */
    private $orders;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForOrders(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasOrdersInterface::PROPERTY_NAME_ORDERS,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForOrders(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @param Collection|Order[] $orders
     *
     * @return self
     */
    public function setOrders(Collection $orders): HasOrdersInterface
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * @param Order|null $order
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrder(
        ?Order $order,
        bool $recip = true
    ): HasOrdersInterface {
        if ($order === null) {
            return $this;
        }

        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            if ($this instanceof ReciprocatesOrderInterface && true === $recip) {
                $this->reciprocateRelationOnOrder($order);
            }
        }

        return $this;
    }

    /**
     * @param Order $order
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrder(
        Order $order,
        bool $recip = true
    ): HasOrdersInterface {
        $this->orders->removeElement($order);
        if ($this instanceof ReciprocatesOrderInterface && true === $recip) {
            $this->removeRelationOnOrder($order);
        }

        return $this;
    }

    /**
     * Initialise the orders property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initOrders()
    {
        $this->orders = new ArrayCollection();

        return $this;
    }
}
