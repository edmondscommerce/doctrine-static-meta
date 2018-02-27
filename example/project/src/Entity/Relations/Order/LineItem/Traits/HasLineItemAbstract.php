<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Order\LineItem\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Order\LineItem;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\HasLineItemInterface;
use My\Test\Project\Entity\Relations\Order\LineItem\Interfaces\ReciprocatesLineItemInterface;

trait HasLineItemAbstract
{
    /**
     * @var LineItem|null
     */
    private $lineItem;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyDoctrineMetaForLineItem(ClassMetadataBuilder $builder): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function getPropertyValidatorMetaForLineItems(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(HasLineItemInterface::PROPERTY_NAME_LINE_ITEM, new Valid());
    }

    /**
     * @return LineItem|null
     */
    public function getLineItem(): ?LineItem
    {
        return $this->lineItem;
    }

    /**
     * @param LineItem $lineItem
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setLineItem(LineItem $lineItem, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesLineItemInterface && true === $recip) {
            $this->reciprocateRelationOnLineItem($lineItem);
        }
        $this->lineItem = $lineItem;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeLineItem(): UsesPHPMetaDataInterface
    {
        $this->lineItem = null;

        return $this;
    }
}
