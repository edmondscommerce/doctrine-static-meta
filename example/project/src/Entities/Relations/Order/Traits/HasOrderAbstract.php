<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Order\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\Entities\Relations\Order\Interfaces\ReciprocatesOrder;
use My\Test\Project\Entities\Order;

trait HasOrderAbstract
{
    /**
     * @var Order|null
     */
    private $order = null;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForOrder(ClassMetadataBuilder $builder);

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setOrder(Order $order, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesOrder && true === $recip) {
            $this->reciprocateRelationOnOrder($order);
        }
        $this->order = $order;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeOrder(): UsesPHPMetaDataInterface
    {
        $this->order = null;

        return $this;
    }
}
