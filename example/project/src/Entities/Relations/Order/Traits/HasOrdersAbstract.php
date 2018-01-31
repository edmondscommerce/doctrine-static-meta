<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Order;

trait HasOrdersAbstract
{
    /**
     * @var ArrayCollection|Order[]
     */
    private $orders;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForOrders(ClassMetadataBuilder $builder);

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @param Collection $orders
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
     */
    public function addOrder(Order $order, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            if (true === $recip) {
                $this->reciprocateRelationOnOrder($order, false);
            }
        }

        return $this;
    }

    /**
     * @param Order $order
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeOrder(Order $order, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->orders->removeElement($order);
        if (true === $recip) {
            $this->removeRelationOnOrder($order, false);
        }

        return $this;
    }

    private function initOrders()
    {
        $this->orders = new ArrayCollection();

        return $this;
    }
}
