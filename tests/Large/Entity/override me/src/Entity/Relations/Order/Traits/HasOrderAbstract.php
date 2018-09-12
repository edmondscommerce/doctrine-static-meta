<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Interfaces\OrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;

/**
 * Trait HasOrderAbstract
 *
 * The base trait for relations to a single Order
 *
 * @package Test\Code\Generator\Entity\Relations\Order\Traits
 */
// phpcs:enable
trait HasOrderAbstract
{
    /**
     * @var Order|null
     */
    private $order;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForOrder(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForOrder(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasOrderInterface::PROPERTY_NAME_ORDER,
            new Valid()
        );
    }

    /**
     * @param null|OrderInterface $order
     * @param bool                         $recip
     *
     * @return HasOrderInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeOrder(
        ?OrderInterface $order = null,
        bool $recip = true
    ): HasOrderInterface {
        if (
            $this instanceof ReciprocatesOrderInterface
            && true === $recip
        ) {
            if (!$order instanceof EntityInterface) {
                $order = $this->getOrder();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $order->$remover($this, false);
        }

        return $this->setOrder(null, false);
    }

    /**
     * @return OrderInterface|null
     */
    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    /**
     * @param OrderInterface|null $order
     * @param bool                         $recip
     *
     * @return HasOrderInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrder(
        ?OrderInterface $order,
        bool $recip = true
    ): HasOrderInterface {

        $this->setEntityAndNotify('order', $order);
        if (
            $this instanceof ReciprocatesOrderInterface
            && true === $recip
            && null !== $order
        ) {
            $this->reciprocateRelationOnOrder($order);
        }

        return $this;
    }
}
