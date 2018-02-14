<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Order\LineItem\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\EntityRelations\Order\LineItem\Interfaces\ReciprocatesLineItem;
use My\Test\Project\Entities\Order\LineItem;

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
    abstract public static function getPropertyMetaForLineItem(ClassMetadataBuilder $builder): void;

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
        if ($this instanceof ReciprocatesLineItem && true === $recip) {
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
