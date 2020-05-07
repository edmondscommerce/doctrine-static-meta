<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Attribute;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Attribute\HasWeightEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Attribute\WeightEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Attribute\WeightEmbeddable;

trait HasWeightEmbeddableTrait
{
    /**
     * @var WeightEmbeddableInterface
     */
    private WeightEmbeddableInterface $weightEmbeddable;

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForWeightEmbeddable(ClassMetadataBuilder $builder): void
    {
        $builder->addLifecycleEvent(
            'postLoadSetOwningEntityOnWeightEmbeddable',
            Events::postLoad
        );
        $builder->createEmbedded(
            HasWeightEmbeddableInterface::PROP_WEIGHT_EMBEDDABLE,
            WeightEmbeddable::class
        )
                ->setColumnPrefix(
                    HasWeightEmbeddableInterface::COLUMN_PREFIX_WEIGHT
                )
                ->build();
    }

    /**
     * @return mixed
     */
    public function getWeightEmbeddable(): WeightEmbeddableInterface
    {
        return $this->weightEmbeddable;
    }

    public function postLoadSetOwningEntityOnWeightEmbeddable(): void
    {
        $this->weightEmbeddable->setOwningEntity($this);
    }

    /**
     * Called at construction time
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function initWeightEmbeddable(): void
    {
        $this->setWeightEmbeddable(
            new WeightEmbeddable(
                WeightEmbeddableInterface::DEFAULT_UNIT,
                WeightEmbeddableInterface::DEFAULT_VALUE
            ),
            false
        );
    }

    /**
     * @param WeightEmbeddableInterface $weightEmbeddable
     *
     * @param bool                      $notify
     *
     * @return $this
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function setWeightEmbeddable(WeightEmbeddableInterface $weightEmbeddable, bool $notify = true): self
    {
        $this->weightEmbeddable = $weightEmbeddable;
        $this->weightEmbeddable->setOwningEntity($this);
        if (true === $notify) {
            $this->notifyEmbeddablePrefixedProperties(
                HasWeightEmbeddableInterface::PROP_WEIGHT_EMBEDDABLE
            );
        }

        return $this;
    }
}
