<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order as Order;
use My\Test\Project\Entity\Relations\Order\Interfaces\HasOrderInterface;
use My\Test\Project\Entity\Relations\Order\Interfaces\ReciprocatesOrderInterface;

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
    abstract public static function getPropertyDoctrineMetaForOrder(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForOrder(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            HasOrderInterface::PROPERTY_NAME_ORDER,
            new Valid()
        );
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     * @param bool                $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setOrder(
        ?Order $order,
        bool $recip = true
    ): HasOrderInterface {

        $this->order = $order;
        if ($this instanceof ReciprocatesOrderInterface
            && true === $recip
            && null !== $order
        ) {
            $this->reciprocateRelationOnOrder($order);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function removeOrder(): HasOrderInterface
    {
        $this->order = null;

        return $this;
    }
}
