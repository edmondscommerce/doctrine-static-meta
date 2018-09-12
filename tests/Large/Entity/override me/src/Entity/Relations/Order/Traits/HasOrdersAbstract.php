<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\OrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrdersInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;

/**
 * Trait HasOrdersAbstract
 *
 * The base trait for relations to multiple Orders
 *
 * @package Test\Code\Generator\Entity\Relations\Order\Traits
 */
// phpcs:enable
trait HasOrdersAbstract
{
    /**
     * @var ArrayCollection|OrderInterface[]
     */
    private $orders;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForOrders(
        ValidatorClassMetaData $metadata
    ): void {
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
    abstract public static function metaForOrders(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|OrderInterface[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @param Collection|OrderInterface[] $orders
     *
     * @return self
     */
    public function setOrders(
        Collection $orders
    ): HasOrdersInterface {
        $this->setEntityCollectionAndNotify(
            'orders',
            $orders
        );

        return $this;
    }

    /**
     * @param OrderInterface|null $order
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrder(
        ?OrderInterface $order,
        bool $recip = true
    ): HasOrdersInterface {
        if ($order === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('orders', $order);
        if ($this instanceof ReciprocatesOrderInterface && true === $recip) {
            $this->reciprocateRelationOnOrder(
                $order
            );
        }

        return $this;
    }

    /**
     * @param OrderInterface $order
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrder(
        OrderInterface $order,
        bool $recip = true
    ): HasOrdersInterface {
        $this->removeFromEntityCollectionAndNotify('orders', $order);
        if ($this instanceof ReciprocatesOrderInterface && true === $recip) {
            $this->removeRelationOnOrder(
                $order
            );
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
