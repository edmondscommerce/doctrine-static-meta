<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order;
use My\Test\Project\EntityRelations\Order\Interfaces\ReciprocatesOrder;

trait HasOrdersAbstract
{
    /**
     * @var ArrayCollection|Order[]
     */
    private $orders;

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForOrders(ClassMetadataBuilder $manyToManyBuilder): void;

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
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setOrders(Collection $orders): UsesPHPMetaDataInterface
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * @param Order $order
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOrder(Order $order, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            if ($this instanceof ReciprocatesOrder && true === $recip) {
                $this->reciprocateRelationOnOrder($order);
            }
        }

        return $this;
    }

    /**
     * @param Order $order
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrder(Order $order, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->orders->removeElement($order);
        if ($this instanceof ReciprocatesOrder && true === $recip) {
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
